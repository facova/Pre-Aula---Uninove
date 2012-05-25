<?php
/**
 * Engine PHP Application Framework
 * http://seelaz.com.br
 *
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
 * File: ORMLoader.php
 **/

import('engine.db.ORM');

/**
 * @author Silas R. N. Junior
 */
class ORMLoader extends ORM {


	/** Estrategia fetch para o mapeamento
	 * @var mixed
	 */
	private $fetch;

	/** Relacionamentos para carregamento Lazy
	 * @var array
	 */
	private $lazyLoads = array();

	/** Colecoes para serem carregadas
	 * @var array
	 */
	private $eagerCollections = array();

	/** Colecoes lazy
	 * @var array
	 */
	private $lazyCollections = array();

	/**
	 * @param ReflectionORMClass $rflctORM
	 * @param string $alias    Apelido da entidade
	 * @param string $path    Caminho da entidade na arvore
	 * @param int $depth    Profundidade do relacionamento
	 */
	public function ORMLoader(ReflectionORMClass $rflctORM, $alias, $path, $depth = null) {
		$this->setRflctORM($rflctORM);
		$this->setAlias($alias);
		$this->setPath($path);
		$this->setDepth($depth);
	}

	/**
	 * @return array
	 */
	public function getLazyLoads() {
		return $this->lazyLoads;
	}

	/**
	 * @param array $newLazyLoads
	 * @return void
	 */
	public function setLazyLoads($newLazyLoads) {
		$this->lazyLoads = $newLazyLoads;
	}


	/** Constroi a string SQL
	 * @param DbDriver $driver    Driver do banco de dados
	 * @param object $entity    Objeto da entidade
	 * @return void
	 */
	public function buildSQL(ORMRequest $request) {

		$driver = $request->getDriver();
		$entity = $request->getEntity();

		if ($this->getCached()) {
			$sql = $this->getCached();
		} else {

			$sql = "SELECT ";
			$current = $this;

			$columns = array();
			$associations = array();
			$where = array();

			//Itera toda a bagaca
			while ($current) {
				//Colunas
				foreach ($current->getRflctORM()->getColumns() as $column) {
					if ($column->isTransient()) continue;
					$columns[] = $current->getAlias().".".$driver->formatField($column->getColumnName())." AS ".$driver->formatField($current->getAlias()."__".$column->getName());
				}

				//Lazy
				foreach ($current->getLazyLoads() as $lazy) {
					foreach ($lazy->getRflctORM()->getIndexes() as $column) {
						$columns[] = $lazy->getAlias().".".$driver->formatField($column->getColumnName())." AS ".$driver->formatField($lazy->getAlias()."__".$column->getName());
					}
				}
				
				//Versioning
				if ($current->getRflctORM()->isVersioned()) {
					$columns[] = $current->getAlias().".".$driver->formatField($current->getRflctORM()->getVersionColumnName())." AS ".$driver->formatField($current->getAlias()."__".$current->getRflctORM()->getVersionColumnName());
				}

				if ($current->getAssociations()) $associations = array_merge($associations,$current->getAssociations());
				$current = $current->getNext();
			}

			//Colunas
			$sql .= implode(",",$columns);

			//From
			$sql .= " FROM ".$driver->formatTable($this->getRflctORM()->getTableName())." ".$this->getAlias();

			//Joins
			foreach ($associations as $assoc) {
				$sql .= " ".$assoc->getSQLString($driver);
			}

			//indices
			$indexes = $this->getRflctORM()->getIndexes();

			foreach ($indexes as $index) {
				if ($index->isForeignKey()) {
					$foreignKeys = $index->getForeignIndexORMProperties();
					foreach ($foreignKeys as $fkIndex) {
						$where[] = $this->getAlias().".".$driver->formatField($fkIndex->getColumnName())." = ?";
					}
				} else {
					$where[] = $this->getAlias().".".$driver->formatField($index->getColumnName())." = ?";
				}
			}
			$sql .= " WHERE (".implode(" AND ",$where).")";
			$this->setCached($sql);
		}
		return $sql;
	}

	/** Carrega os dados da entidade de um resource de banco
	 * @param DbDriver $driver    Driver do banco de dados
	 * @param object $entity    Entidade a ser atualizada
	 * @param array $result    Array de dados do banco
	 * @return void
	 */
	private function loadData(ORMRequest $request, $result) {

		$driver = $request->getDriver();
		$entity = &$request->getEntity();

		$current = $this;
		while ($current) {
			$exists = false;
			foreach ($current->getRflctORM()->getColumns() as $column) {
				if ($column->isTransient()) continue;
				//$column = new ReflectionORMProperty(); //TODO - Remover
				if (isset($result[$current->getAlias()."__".$column->getName()])) {
					$exists = true;
					if (!isset($currentEntity)) $currentEntity = $current->fetchEntity($entity,true);
					if ((class_exists($column->getType())  && in_array( $column->getType(), get_declared_classes() )) && is_subclass_of($column->getType(),"Enumeration")) {
						$type = $column->getType();
						$column->setValue($currentEntity, new $type((integer)$result[$current->getAlias()."__".$column->getName()]));
					} else {
						$column->setValue($currentEntity, $driver->unformatValue($column->getType(),$result[$current->getAlias()."__".$column->getName()]) );
					}
				}
			}

			//Lazy
			foreach ($current->getLazyLoads() as $lazy) {
				foreach ($lazy->getRflctORM()->getIndexes() as $column) {
					if (isset($result[$lazy->getAlias()."__".$column->getName()])) {
						$currentLazyEntity = $lazy->fetchEntity($entity,true);
						$column->setValue($currentLazyEntity, $driver->unformatValue($column->getType(),$result[$lazy->getAlias()."__".$column->getName()]) );
					}
				}

				if (isset($currentLazyEntity)) {
					if (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString(($currentLazyEntity instanceof IEntityContainer ? $currentLazyEntity->getSubject() : $currentLazyEntity)))) {
						$cachedEntity = &EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString(($currentLazyEntity instanceof IEntityContainer ? $currentLazyEntity->getSubject() : $currentLazyEntity)));
						$lazy->setEntity($entity,$cachedEntity);
					} else {
						$proxy = EntityManager::proxyfy($currentLazyEntity);
						if ($currentLazyEntity) $lazy->setEntity($entity,$proxy);
						$currentLazyEntity = null;
					}
				}
			}
			//Verificar cache de entidade
			if ($exists) {
				if (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($currentEntity))) {
					$cachedEntity = &EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($currentEntity));
					$current->setEntity($entity,$cachedEntity);
				} else {
					if ($current->getRflctORM()->isVersioned()) {
						if (isset($result[$current->getAlias()."__".$current->getRflctORM()->getVersionColumnName()])) {
							$version = $result[$current->getAlias()."__".$current->getRflctORM()->getVersionColumnName()];
							EntityManager::getCacher()->cache(EntityManager::getCacher()->getOIDString($currentEntity),$currentEntity,$version);
						} else {
							die("Nao obtive dados do banco referente a coluna de versao.");
						}
					} else {
						EntityManager::getCacher()->cache(EntityManager::getCacher()->getOIDString($currentEntity),$currentEntity);
					}
				}
			}
			unset($currentEntity);
			$current = $current->getNext();
		}

		//Load collections later
		$current = $this;
		while ($current) {
			$current->loadCollections($request);
			$current = $current->getNext();
		}
	}

	/** Retorna a estrategia fetch para o mapeamento
	 * @return mixed
	 */
	public function getFetch() {
		return $this->fetch;
	}

	/** Define a estrategia fetch para o mapeamento
	 * @param mixed $newFetch
	 * @return void
	 */
	public function setFetch($newFetch) {
		$this->fetch = $newFetch;
	}

	/** Adiciona um relacionamento para carregamento lazy
	 * @param ORMLoader $lazy
	 * @return void
	 */
	public function addLazyLoad(ORMLoader $lazy) {
		$this->lazyLoads[] = $lazy;
	}

	/** Retora os mapeamentos de colecoes para serem carregadas
	 * @return array
	 */
	public function getEagerCollections() {
		return $this->eagerCollections;
	}

	/** Adiciona uma colecao para ser carregada
	 * @param ORMLoader $eager    ORM da colecao a ser carregada
	 * @return void
	 */
	public function addEagerCollection(ORMLoader $eager) {
		$this->eagerCollections[] = $eager;
	}

	/** Retorna as colecoes a serem carregadas
	 * @return array
	 */
	public function getLazyCollections() {
		return $this->lazyCollections;
	}

	/** Adiciona uma colecao a ser carregada de forma Lazy
	 * @param ORMLoader $lazy
	 * @return void
	 */
	public function addLazyCollection(ORMLoader $lazy) {
		$this->lazyCollections[] = $lazy;
	}

	/** Carrega as colecoes
	 * @param DbDriver $driver    Driver do Banco de dados
	 * @param object $entity    Entidade a ser sincronizada
	 * @return void
	 */
	public function loadCollections(ORMRequest $request) {

		$driver = $request->getDriver();
		$entity = &$request->getEntity();

		$currentEntity = $this->fetchEntity($entity);
		
		//sem pai da colecao
		if (!isset($currentEntity)) return;

		foreach ($this->getRflctORM()->getCollections() as $collection) {

			//testando para remover
			//Por que desse loader? pra evitar que a operacao de build ordene usando esse nodo (owner da colecao) novamente
			$builder = new ORMLoaderBuilder();
			//$loader = $builder->build($collection->getTargetEntity(),null,$collection->getName(),$this,$this->getDepth());

			$items = array();
			$collectionRflctORM = EntityManager::getReflectionData($collection->getTargetEntity());
			$collectionIndexes = $collectionRflctORM->getIndexes();

			$where = array();
			$indexes = array();
			if ($collection->isOneToMany()) {
				foreach ($collectionRflctORM->getIndexes() as $index) {
					if ($index->isForeignKey()) {
						$foreignKeys = $index->getForeignIndexORMProperties();
						foreach ($foreignKeys as $fkIndex) {
							$indexes[] = " rt.".$driver->formatField($fkIndex->getColumnName());
						}
					} else {
						$indexes[] = " rt.".$driver->formatField($index->getColumnName());
					}
				}
				$from = " FROM ".$driver->formatTable($collectionRflctORM->getTableName())." rt";

				$thisIndexes = $this->getRflctORM()->getIndexes();
				$join = " LEFT JOIN ".$driver->formatTable($this->getRflctORM()->getTableName())." jn ON (";
				$on = array();
				
				//TODO - Unidirecional
				
				$mappedColumns = ($collectionRflctORM->getORMProperty($collection->getMappedBy())->isComposite() ? $collectionRflctORM->getORMProperty($collection->getMappedBy())->getCompositeColumns() : array($collectionRflctORM->getORMProperty($collection->getMappedBy())->getColumn()));
				for ($i = 0; $i < sizeof($thisIndexes); $i++) {
					$on[] = " jn.".$driver->formatField($thisIndexes[$i]->getColumnName())." = rt.".$driver->formatField($mappedColumns[$i]->getName());
					$where[] = "jn.".$driver->formatField($thisIndexes[$i]->getColumnName())." = ".$driver->formatValue($thisIndexes[$i]->getType(),$thisIndexes[$i]->getValue($currentEntity));
				}
				$join .= implode(" AND ",$on).")";

				$sql = "SELECT ".implode(",",$indexes).$from.$join." WHERE ".implode(" AND ",$where);

			} else if ($collection->isManyToMany()) {

				$joinColumns = (is_array($collection->getJoinColumns()) ? $collection->getJoinColumns() :  array($collection->getJoinColumns()));
				$inverseJoinColumns = (is_array($collection->getInverseJoinColumns()) ? $collection->getInverseJoinColumns() :  array($collection->getInverseJoinColumns()));
				$from = " FROM ".$driver->formatTable($collection->getJoinTable())." jt";
				$thisIndexes = $this->getRflctORM()->getIndexes();

				$join = " LEFT JOIN ".$driver->formatTable($this->getRflctORM()->getTableName())." jn ON (";
				$on = array();
				for ($i = 0; $i < sizeof($joinColumns); $i++) {
					$on[] = " jt.".$driver->formatField($joinColumns[$i])." = jn.".$driver->formatField($thisIndexes[$i]->getColumnName());
					$where[] = "jn.".$driver->formatField($thisIndexes[$i]->getColumnName())." = ".$driver->formatValue($thisIndexes[$i]->getType(),$thisIndexes[$i]->getValue($currentEntity));
					$inverseJoinColumns[$i] = "jt.".$driver->formatField($inverseJoinColumns[$i])." AS ".$collectionIndexes[$i]->getColumnName();
				}
				$join .= implode(" AND ",$on).")";
				$sql = "SELECT ".implode(",",$inverseJoinColumns).$from.$join." WHERE ".implode(" AND ",$where);
			}
			//Executa SQL
			$result = $driver->fetchAssoc($sql);

			//Para cada item da colecao
			foreach ($result as $idx) {
				$className = $collection->getTargetEntity();
				$obj = new $className();
				//TODO - A ordem dos indices deve ser a mesma das join columns ou teremos um problema. Melhorar isso
				for ($i = 0; $i < sizeof($collectionIndexes); $i++) {
					if ($collectionIndexes[$i]->isForeignKey()) {
						$foreignKeys = $collectionIndexes[$i]->getForeignIndexORMProperties();
						foreach ($foreignKeys as $fkIndex) {
							$fkIndex->setValue($obj,$idx[$fkIndex->getColumnName()],true);
						}
					} else {
						$collectionIndexes[$i]->setValue($obj,$idx[$collectionIndexes[$i]->getColumnName()],true);
					}
				}
				//Verifica cache
				If (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($obj))) {
					$obj = EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($obj));
				} else {
					//Carrega
					if ($collection->getFetch() == FetchType::FETCH) {
						DAOFactory::getDAO()->load($obj);
					} else {
						$obj = EntityManager::proxyfy($obj);
					}
				}
				$items[] = $obj;
			}
			//Cria acolecao e atribui a entidade (mesmo se vazia)
			$collectionInstance = new Collection($items);
			$collection->setValue($currentEntity,$collectionInstance);
		}
	}


	private function fillSQL(ORMRequest $request,$sql) {

		$driver = $request->getDriver();
		$entity = &$request->getEntity();

		$currentEntity = $this->fetchEntity($entity);

		//indices
		$indexes = $this->getRflctORM()->getIndexes();
		$pos = 0;
		foreach ($indexes as $index) {
			//Chave estrangeira?
			if ($index->isForeignKey()) {
				//Composta?
				if ($index->isComposite()) {
					foreach ($index->getForeignIndexORMProperties() as $currentIndex) {
						$pos = strpos($sql, "?", $pos);
						$value = $driver->formatValue($index->getType(),$currentIndex->getValue($entity));
						$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
						$pos += strlen($value);
					}
					continue;
					//Simples
				} else {
					$currentIndex = $index->getForeignIndexORMProperties();
					$currentIndex = $currentIndex[0];
				}
				//Chave Primaria
			} else {
				$currentIndex = $index;
			}
			$pos = strpos($sql, "?", $pos);
			$value = $driver->formatValue($index->getType(),$currentIndex->getValue($currentEntity));
			$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
			$pos += strlen($value);
		}
		return $sql;
	}

	/** Executa a sincronia da(s) entidade(as) com o banco de dados
	 * @param ORMRequest $request    Requisicao ORM
	 * @return void
	 */
	public function sync(ORMRequest $request) {
		
		$driver = $request->getDriver();
		
		$sql = $this->buildSQL($request);
		$preparedSQL = $this->fillSQL($request,$sql);
		$result = $driver->fetchAssoc($preparedSQL);
		if (sizeof($result) > 0) {
			$this->loadData($request,$result[0]);
		} else {
			//Caso nao tenha encontrado nada
			if (sizeof($result) == 0) {
				throw new ORMException("Load Retornou 0 Registros");
			}
		}
	}
}

?>
