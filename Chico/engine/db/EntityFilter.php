<?php
/**
 * Engine PHP Application Framework
 * http://seelaz.com.br
 * Copyright (C) 2006-2011 Silas "Seelaz" Junior <seelaz@gmail.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @license http://www.fsf.org/licensing/licenses/gpl.html
 *
 * @filesource
 * @package engine
 * @subpackage db
 * File: EntityFilter.php
 **/

import('engine.db.FilterParameter');

/** Objeto de criacao de crietrios de busca
 * @author Silas R. N. Junior
 */
class EntityFilter {

	/** DAO da requisicao
	 * @var DAO
	 */
	private $dao;

	/** Classe a qual o filtro se refere
	 * @var ReflectionORMClass
	 */
	private $class;

	/** Array das expressoes do filtro
	 * @var array
	 */
	private $expressions = array ();

	/** Apelido da entidade
	 * @var string
	 */
	private $alias;

	/** Array dos apelidos dos caminhos de entidades compomentes
	 * @var array
	 */
	private $aliases = array();

	/** Array de estrategias de recuperacao dos caminhos da classe
	 * @var array
	 */
	private $fetchModes = array();

	/** Numero de registros a saltar
	 * @var int
	 */
	private $offset;

	/** Quantidade máxima de registros a recuperar
	 * @var int
	 */
	private $limit;

	/**
	 * @testing
	 */
	private $distinctResult = false;

	/** Campos para ordenar o resultado
	 * @var array
	 */
	private $orderBy = array();

	/** Campos para agrupar o resultado
	 * @var array
	 */
	private $groupBy = array();

	/** Construtor
	 * @param string $className   Nome da classe para o filtro
	 * @param string $alias   Apelido da entidade
	 */
	public function EntityFilter(DAO $dao,$className, $alias = null) {
		$this->dao = $dao;
		
		if (class_exists($className)) {
			$this->class = EntityManager::getReflectionData($className);
		} else {
			throw new Exception("Erro criando EntityFilter: Classe {$className} inexistente.");
		}
		
		if (isset($alias)) {
			$this->setAlias($alias);
		} else {
			$this->setAlias('TB001');
		}
	}

	/** Retorna o DAO a ser utilizado nas operacoes subsequentes
	 * @return DAO
	 */
	public function getDAO() {
		return $this->dao;
	}

	public function setDistinctResult($value) {
		$this->distinctResult = $value;
	}

	private function isDistinctResult() {
		return $this->distinctResult;
	}

	/** Limita a quantidade de registros a recuperar
	 * @param int $number   Numero de registros maximo a recuperar
	 * @return EntityFilter
	 */
	public function limit($number) {
		$this->limit = $number;
		return $this;
	}

	/** Define o numero de registros a saltar antes de recuperar
	 * @param int $number   Numero de registros
	 * @return EntityFilter
	 */
	public function offset($number) {
		$this->offset = $number;
		return $this;
	}

	/** Adiciona uma condicao ao filtro
	 * @param IFilterCondition $condition
	 * @return EntityFilter
	 */
	public function add(IFilterCondition $condition) {
		$this->expressions[] = $condition;
		return $this;
	}

	/** Define o modo de recuperacao de uma entidade componente
	 * @param string $alias   Apelido referenciando o caminho da Entidade associada
	 * @param int $type   Tipo da estrategia
	 * @return void
	 */
	public function setFetchMode($alias, $type) {

		//Resolve a string de caminho para a propriedade
		$fullPath = "";
		$splitAlias = explode(".",$alias);
		if (count($splitAlias) > 1) {
			$fullPath = $splitAlias[1];
			if(isset($this->aliases[$splitAlias[0]])) {
				$path = $this->aliases[$splitAlias[0]]['path'];
				$resolve = true;
				while($resolve == true) {
					$splitPath = explode(".",$path);
					if (count($splitPath) > 1) {
						if (isset($this->aliases[$splitPath[0]])) {
							$fullPath = $splitPath[1].".".$fullPath;
							$path =  $this->aliases[$splitPath[0]]['path'];
						} else {
							$resolve = false;
							$fullPath = $splitPath[0].".".$fullPath;
						}
					} else {
						$resolve = false;
						$fullPath = $splitPath[0].".".$fullPath;
					}
				}
			}
		} else {
			if ($this->getClass()->getReflectionClass()->hasProperty($alias)) {
				//$pd = $this->getEntityData()->getPropertyData($alias);
				$fullPath = $alias;
			} else {
				throw new Exception("Propriedade ou Alias {$alias} nao definido.");
			}
		}
		//$pd->setFetchMode($type);
		$this->fetchModes[$fullPath] = $type;
	}

	/** Retorna o modo de recuperacao de uma entidade componente
	 * @param string $path   Caminho da Entidade associada
	 * @return void
	 */
	public function getFetchMode($path) {
		$path = str_replace("root#","",str_replace("root#.","",$path));
		if (isset($this->fetchModes[$path])&& !is_null($this->fetchModes[$path])) {
			return $this->fetchModes[$path];
		} else {
			$class = $this->getClass();
			$fullPath = explode(".",$path);
			if (sizeof($fullPath) > 1) {
				for($i = 0; $i < (sizeof($fullPath) -1); $i++) {
					$class = EntityManager::getReflectionData($class->getORMProperty($fullPath[$i])->getType());
				}
			}
			return $class->getORMProperty($fullPath[sizeof($fullPath) - 1])->getFetch();
		}
	}

	/** Cria um apelido para um relacionamento
	 * @param string $path   Caminho da associacao a qual o alias se refere
	 * @param string $alias   Apelido da associacao
	 * @param int $joinType   Tipo de Join a ser usado na associacao
	 * @return void
	 */
	public function createAlias($path, $alias, $joinType = JoinType::INNER_JOIN) {
		
		if (isset($this->aliases[$alias])) {
			throw new Exception("Erro ao criar alias. Alias [{$alias}] já definido.");
		}
		
		$splitPath = explode(".",$path);
		if (count($splitPath) > 1 ) {
			//******************************* HACK - trabalhando com interfaces **********************************
			if (interface_exists($this->aliases[$splitPath[0]]['type']))
			$this->aliases[$splitPath[0]]['type'] = substr($this->aliases[$splitPath[0]]['type'],1);
			$type = EntityManager::getReflectionData($this->aliases[$splitPath[0]]['type'])->getORMProperty($splitPath[1])->getType();
		} else {
			$type = $this->getClass()->getORMProperty($splitPath[0])->getType();
		}
		$this->aliases[$alias] = array( "path" => $path, "joinType" => $joinType, "type" => $type);
	}

	/** Retorna a string SQL do filtro
	 * @return string
	 */
	public function getSqlString() {

		$driver = $this->getDAO()->getDriver();
		$ql = array();
		$joins = array();
		foreach ($this->expressions as $condition) {
			$ql[] = $condition->toSql($this);
		}

		//Heranca
		
		$currentClass = array();
		$parentClass = array();
		
		if ($this->getClass()->getParentClass() && $this->getClass()->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
			
			$i = 0;
			$parent = $this->getClass()->getParentORMClass();
			$currentClass[$i] = $this->getClass();
			$parentClass[$i] = $parent;
			while ($parent->getParentClass()) {
				$i++;
				$currentClass[$i] = $parent;
				$parentClass[$i] = $parent->getParentORMClass();
				$parent = $parent->getParentORMClass();
			}
			$tableName = $parent->getTableName();
			$thisIndexes = $parent->getIndexes();
			$alias = $this->getAlias().$tableName;
			$parentIndex = $parentClass[$i]->getIndexes();
			$on = array();
			for ($j = $i; $j>=0; $j--) {
				$join = " INNER JOIN ".$currentClass[$j]->getTableName()." ".$this->getAlias().$currentClass[$j]->getTableName()." ON (";
				
				//Com join column
				if ($currentClass[$j]->hasAnnotation("JoinColumn")) {
					$currentIndex = array($currentClass[$j]->getAnnotation("JoinColumn"));
					$on[] = $this->getAlias().$currentClass[$j]->getTableName().".".$currentIndex[0]->getName()." = ".$this->getAlias().$parentClass[$j]->getTableName().".".$parentIndex[0]->getColumnName();
					
				//Indice composto
				} else if ($currentClass[$j]->hasAnnotation("CompositeColumnAnnotation")) {
					$k = 0;
					foreach ($currentClass[$j]->getAnnotation("CompositeColumnAnnotation")->getColumns() as $currentIndex) {
						$on[] = $this->getAlias().$currentClass[$j]->getTableName().".".$currentIndex->getName()." = ".$this->getAlias().$parentClass[$j]->getTableName().".".$parentIndex[$k]->getColumnName();
						$k++;
					}
				//Padrao
				} else {
					foreach ($parentClass[$i]->getIndexes() as $index) {
						$on[] = $this->getAlias().$currentClass[$j]->getTableName().".".$index->getColumnName()." = ".$this->getAlias().$parentClass[$j]->getTableName().".".$index->getColumnName();
					}
				}
				
				$joins[] = $join.implode(" AND ",$on).")";
			}
		} else {
			$tableName = $this->getClass()->getTableName();
			$alias = $this->getAlias();
			$thisIndexes = $this->getClass()->getIndexes();
		}
		
		foreach ( array_keys($this->aliases) as $refrAlias ) {
			//Extrai o Fetch mode mapeado, encontra os dados da classe do nodo atual
			$splitPath = explode(".",$this->aliases[$refrAlias]['path']);
			if (count($splitPath) > 1 ) {
				$thisClass = EntityManager::getReflectionData($this->aliases[$splitPath[0]]['type']);
				$property = $thisClass->getORMProperty($splitPath[1]);
				$thisAlias = $splitPath[0];
			} else {
				$thisClass = $this->getClass();
				$property = $thisClass->getORMProperty($this->aliases[$refrAlias]['path']);
				$table = $thisClass->getTableName();
				$thisAlias = $this->getAlias();
			}
			if ($this->aliases[$refrAlias]['joinType'] == JoinType::INNER_JOIN) {
				$joinTypeStr = ' INNER JOIN ';
			} else if ($this->aliases[$refrAlias]['joinType'] == JoinType::LEFT_JOIN) {
				$joinTypeStr = ' LEFT JOIN ';
			}

			if ($property->isOneToOne()) {

				//trabalhando com interfaces
				if (interface_exists(trim($property->getType()))) {
					$refrClass = EntityManager::getReflectionData($property->getTargetEntity());
				} else {
					$refrClass = EntityManager::getReflectionData($property->getType());
				}
				$joinedTable = $refrClass->getTableName();

				$join = $joinTypeStr.$joinedTable." ".$refrAlias." ON (";
				if ($property->getMappedBy()) { //MappedBy
					$thisIndexes = $thisClass->getIndexes();
					$refrProperty = $thisClass->getORMProperty($property->getMappedBy());
					if ($refrProperty->isComposite()) {

					} else {
						$refrColumns = array($refrProperty->getColumn());
					}
					$on = array();
					for ($i = 0; $i < sizeof($refrColumns); $i++) {
						$on[] = " ".$thisAlias.".".$driver->formatField($thisIndexes[$i]->getColumnName())." = ".$refrAlias.".".$driver->formatField($refrColumns[$i]->getName());
					}
				} else { //MappedIn
					$refrIndexes = $refrClass->getIndexes();
					if ($property->isComposite()) {

					} else {
						$thisColumns = array($property->getColumn());
					}
					$on = array();
					for ($i = 0; $i < sizeof($thisColumns); $i++) {
						$on[] = " ".$thisAlias.".".$driver->formatField($thisColumns[$i]->getName())." = ".$refrAlias.".".$driver->formatField($refrIndexes[$i]->getColumnName());
					}
				}
				$joins[] = $join.implode(" AND ",$on).")";
			} else
			if ($property->isOneToMany()) {
				//trabalhando com interfaces, uso de targetEntity obrigatorio
				$refrClass = EntityManager::getReflectionData($property->getTargetEntity());
				$joinedTable = $refrClass->getTableName();
				$join = $joinTypeStr.$joinedTable." ".$refrAlias." ON (";
				if ($property->getMappedBy()) { //MappedBy
					$thisIndexes = $thisClass->getIndexes();
					$refrProperty = $thisClass->getORMProperty($property->getMappedBy());
					if ($refrProperty->isComposite()) {

					} else {
						$refrColumns = array($refrProperty->getColumn());
					}
					$on = array();
					for ($i = 0; $i < sizeof($refrColumns); $i++) {
						$on[] = " ".$thisAlias.".".$driver->formatField($thisIndexes[$i]->getColumnName())." = ".$refrAlias.".".$driver->formatField($refrColumns[$i]->getName());
					}
					$joins[] = $join.implode(" AND ",$on).")";
				} else {
					throw new Exception("A propriedade MappedBy e obrigatoria em @OneToMany na propriedade ".$thisClass->getName().".".$property->getName()." Utilize @OneToMany(mappedBy=<nome da coluna na entidade da lista>)");
				}
			} else
			if ($property->isManyToOne()) {
				//trabalhando com interfaces
				if (interface_exists(trim($property->getType()))) {
					$refrClass = EntityManager::getReflectionData($property->getTargetEntity());
				} else {
					$refrClass = EntityManager::getReflectionData($property->getType());
				}
				$joinedTable = $refrClass->getTableName();

				$join = $joinTypeStr.$joinedTable." ".$refrAlias." ON (";
				if ($property->getMappedBy()) { //MappedBy
					$thisIndexes = $thisClass->getIndexes();
					$refrProperty = $thisClass->getORMProperty($property->getMappedBy());
					if ($refrProperty->isComposite()) {

					} else {
						$refrColumns = array($refrProperty->getColumn());
					}
					$on = array();
					for ($i = 0; $i < sizeof($refrColumns); $i++) {
						$on[] = " ".$thisAlias.".".$driver->formatField($thisIndexes[$i]->getColumnName())." = ".$refrAlias.".".$driver->formatField($refrColumns[$i]->getName());
					}
				} else { //MappedIn
					$refrIndexes = $refrClass->getIndexes();
					if ($property->isComposite()) {

					} else {
						$thisColumns = array($property->getColumn());
					}
					$on = array();
					for ($i = 0; $i < sizeof($thisColumns); $i++) {
						$on[] = " ".$thisAlias.".".$driver->formatField($thisColumns[$i]->getName())." = ".$refrAlias.".".$driver->formatField($refrIndexes[$i]->getColumnName());
					}
				}
				$joins[] = $join.implode(" AND ",$on).")";
			} else
			if ($property->isManyToMany()) {
				//trabalhando com interfaces, uso de targetEntity obrigatorio
				$refrClass = EntityManager::getReflectionData($property->getTargetEntity());
				$refrIndexes = $refrClass->getIndexes();
				$refrTable = $refrClass->getTableName();
				$colAlias = "col_".$refrAlias;

				$joinColumns = (is_array($property->getJoinColumns()) ? $property->getJoinColumns() :  array($property->getJoinColumns()));
				$inverseJoinColumns = (is_array($property->getInverseJoinColumns()) ? $property->getInverseJoinColumns() :  array($property->getInverseJoinColumns()));
				$colTable = $property->getJoinTable();
				
				$join = " LEFT JOIN ".$driver->formatTable($this->getRflctORM()->getTableName())." ".$colAlias." ON (";
				$invJoin = " LEFT JOIN ".$driver->formatTable($refrTable)." ".$refrAlias." ON (";
				$onJoin = array();
				$onInverse = array();
				for ($i = 0; $i < sizeof($joinColumns); $i++) {
					$onJoin[] = " ".$thisAlias.".".$driver->formatField($joinColumns[$i])." = ".$colAlias.".".$driver->formatField($thisIndexes[$i]->getColumnName());
					$onInverse[] = " ".$colAlias.".".$driver->formatField($inverseJoinColumns[$i])." = ".$refrAlias.".".$driver->formatField($refrIndexes[$i]->getColumnName());
				}
				$joins[] = $join.implode(" AND ",$onJoin).")";
				$joins[] = $invJoin.implode(" AND ",$onInverse).")";
			}
		}
			
		$select = $this->isDistinctResult() ? $sql."SELECT DISTINCT " : "SELECT ";
			
		$fields = array();
		foreach ($thisIndexes as $index) {
			$fields[] = $alias.".".$driver->formatField($index->getColumnName())." AS ".$index->getName();
		}
		
		$sql = $select.implode(",",$fields)		
		." FROM ".$driver->formatTable($tableName)." ".$alias
		.implode(" \n",$joins)
		." WHERE ";
		if (count($ql) > 0) {
			$sql .= implode(" AND ",$ql);
		} else {
			$sql .= "1=1";
		}
			
		if (count($this->groupBy) > 0) {
			$grp = array();
			$sql .= " GROUP BY ";
			foreach ($this->groupBy as $group) {
				$grp[] = $group->toSql($this);
			}
			$sql .= implode(",",$grp);
			$grp = null;
		}
			
		if (count($this->orderBy) > 0) {
			$ord = array();
			$sql .= " ORDER BY ";
			foreach ($this->orderBy as $order) {
				$ord[] = $order->toSql($this);
			}
			$sql .= implode(",",$ord);
			$ord = null;
		}
			
		if (isset($this->limit)) {
			$sql .= $this->getDAO()->getDriver()->limit($this->limit,$this->offset);
		}
		//echo "<br/>".$sql."<br/>";
		return $sql;
	}

	/** Retorna a classe da o filtro
	 * @return ReflectionORMClass
	 */
	public function getClass() {
		return $this->class;
	}

	/** Retorna o apelido definido para a entidade encapsulada
	 * @return string
	 */
	public function getAlias() {
		return $this->alias;
	}

	/** Define o apelido definido para a entidade encapsulada
	 * @param string $newAlias   Apelido da entidade
	 * @return void
	 */
	public function setAlias($newAlias) {
		$this->alias = $newAlias;
	}

	/** Retorna o EntityFilter referente ao apelido fornecido
	 * @param string $name   Apelido da Entidade
	 * @return EntityFilter
	 */
	public function getFilterByAlias($name) {
		if ($this->getAlias() == $name) {
			return $this;
		} else {
			foreach ($this->aliases as $alias) {
				$al = $alias->getFilterByAlias($name);
			}
			if ($al) {
				return $al;
			}
		}
		throw new Exception("Nao existe uma entidade com o alias {$name} mapeada para entidade ".$this->getClass()->getName());
	}

	/** Retorna o array de apelidos do filtro
	 * @return array
	 */
	public function getAliases() {
		return $this->aliases;
	}

	/** Retorna um array com as entidades econtradas utilizando o filtro
	 * @return array
	 */
	public function getList() {
		$objects = array();
		$sql = $this->getSqlString();
		if (!$this->getDAO()->isOngoingTransaction()) {
			$this->getDAO()->getDriver()->connect();
		}
		$data = $this->getDAO()->getDriver()->fetchAssoc($sql);
		if (!$this->getDAO()->isOngoingTransaction()) {
			$this->getDAO()->getDriver()->disconnect();
		}
		foreach ($data as $res) {
			$thisClass = $this->getClass()->getName();
			$object = new $thisClass();
			if ($this->getClass()->getParentClass() && $this->getClass()->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
				$parent = $this->getClass()->getParentORMClass();
				while ($parent->getParentClass()) {
					$parent = $parent->getParentORMClass();
				}
				$thisIndexes = $parent->getIndexes();
			} else {
				$thisIndexes = $this->getClass()->getIndexes();
			}
			foreach ($thisIndexes as $index) {
				$index->setValue($object,$this->getDAO()->getDriver()->formatValue($index->getType(),$res[$index->getName()]));
			}
			$objects[] = $object;
			$object = null;
		}
		if (count($data) > 0) $this->getDAO()->load($objects,$this);
		return $objects;
	}

	/** Retorna o Objeto encontrado
	 * @return object
	 */
	public function getUnique() {
		$sql = $this->getSqlString(); //.$this->getDAO()->getDriver()->limit(1);
		if (!$this->getDAO()->isOngoingTransaction()) {
			$this->getDAO()->getDriver()->connect();
		}
		$data = $this->getDAO()->getDriver()->fetchAssoc($sql);
		if (!$this->getDAO()->isOngoingTransaction()) {
			$this->getDAO()->getDriver()->disconnect();
		}
		if (count($data) == 1) {
			$thisClass = $this->getClass()->getName();
			$object = new $thisClass();
			if ($this->getClass()->getParentClass() && $this->getClass()->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
				$parent = $this->getClass()->getParentORMClass();
				while ($parent->getParentClass()) {
					$parent = $parent->getParentORMClass();
				}
				$thisIndexes = $parent->getIndexes();
			} else {
				$thisIndexes = $this->getClass()->getIndexes();
			}
			foreach ($thisIndexes as $index) {
				$index->setValue($object,$this->getDAO()->getDriver()->formatValue($index->getType(),$data[0][$index->getName()]));
			}
			$this->getDAO()->load($object);
			return $object;
		} else if (count($data) > 1) {
			throw new Exception("Mais de um resultado encontrado utilizando o filtro.");
		}
		return null;
	}

	/** Retorna o Objeto de metadados da entidade
	 * @return EntityData
	 * @deprecated
	 */
	protected function getEntityData() {
		die("Metodo marcado como deprecated");
		return $this->entityData;
	}

	/** Define o Objeto de metadados da entidade
	 * @param EntityData $newEntityData
	 * @return void
	 * @deprecated
	 */
	protected function setEntityData(EntityData $newEntityData) {
		die("Metodo marcado como deprecated");
		$this->entityData = $newEntityData;
	}

	/** Adiciona uma propriedade para ordenar o resultado
	 * @param FilterOrderBy $order
	 * @return EntityFilter
	 */
	public function addOrderBy(FilterOrderBy $order) {
		$this->orderBy[] = $order;
		return $this;
	}

	/** Adiciona uma propriedade para agrupar o resultado
	 * @param FilterGroupBy $group
	 * @return EntityFilter
	 */
	public function addGroupBy(FilterGroupBy $group) {
		$this->groupBy[]  = $group;
		return $this;
	}

	/** Retorna um array de propriedades para ordenar o resultado
	 * @return array
	 */
	public function getOrdering() {
		return $this->orderBy;
	}

	/** Retorna um array de propriedades para agrupar o resultado
	 * @return array
	 */
	public function getGrouping() {
		return $this->groupBy;
	}

	/**
	 * Retorna o caminho completo do alias
	 * @param string $alias
	 * @return string path mapeado
	 */
	public function getPathByAlias($alias) {
		//Resolve a string de caminho para a propriedade
		$fullPath = "";
		$splitAlias = explode(".",$alias);
		if (count($splitAlias) > 1) {
			$fullPath = $splitAlias[1];
			if(isset($this->aliases[$splitAlias[0]])) {
				$path = $this->aliases[$splitAlias[0]]['path'];
				$resolve = true;
				while($resolve == true) {
					$splitPath = explode(".",$path);
					if (count($splitPath) > 1) {
						if (isset($this->aliases[$splitPath[0]])) {
							$fullPath = $splitPath[1].".".$fullPath;
							$path =  $this->aliases[$splitPath[0]]['path'];
						} else {
							$resolve = false;
							$fullPath = $splitPath[0].".".$fullPath;
						}
					} else {
						$resolve = false;
						$fullPath = $splitPath[0].".".$fullPath;
					}
				}
			} else {
				throw new Exception("Propriedade ou Alias [{$splitAlias[0]}] nao definido.");
			}
		} else {
			if ($this->getClass()->hasProperty($alias)) {
				$fullPath = $alias;
			} else if ($this->aliases[$alias]) {
				$fullPath = $this->getPathByAlias($this->aliases[$alias]['path']);
			} else {
				throw new Exception("Propriedade ou Alias [{$alias}] nao definido.");
			}
		}
		return $fullPath;
	}
}

?>