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
 * File: ReflectionORMClass.php
 **/

import('engine.reflection.ReflectionClassAnnotated');

/** Classe de reflexao com dados ORM
 * @author Silas R. N. Junior
 */
class ReflectionORMClass extends ReflectionClassAnnotated {

	/** Objeto de reflexao da superclasse (se houver)
	 * @var ReflectionORMClass
	 */
	private $parentORMClass;

	/** Objeto de reflexao da subclasse (se houver)
	 * @var ReflectionORMClass
	 */
	private $subORMClass;

	/** Colunas da tabela
	 * @var array
	 */
	private $columns = array();

	/** Composicoes
	 * @var array
	 */
	private $joins = array();

	/** Colecoes
	 * @var array
	 */
	private $collections = array();

	/** Indices
	 * @var array
	 */
	private $indexes = array();

	/** Classes mapeadas por relacionamentos
	 * @var array
	 */
	private $mappedClassProperties;
	
	private $properties = array();


	/**
	 * @param mixed $argument
	 */
	public function ReflectionORMClass($argument) {
		try {
			parent::ReflectionClassAnnotated($argument);
		} catch (ReflectionException $e) {
			throw new ReflectionException("Problemas com as Annotations da classe ".parent::getName().".\n".$e->getMessage());
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		//Verifica se e uma entidade, senao dispara uma exception
		if (!$this->hasAnnotation('Entity')) throw new ORMException("[ORM] A classe [{$this->getName()}] nao foi anotada como entidade. Verifique se a classe possui a anotacao @Entity.");
		//Verifica se o segundo (ou terceiro) parametro do construtor é o objeto de reflexao de uma subclasse ou uma propriedade de indice FK
		if (func_num_args() == 2) {
			$arg1 = func_get_arg(1);
			if ($arg1 instanceof ReflectionORMClass) $this->subORMClass = $arg1;
			//FK
			else
			if ($arg1 instanceof ReflectionORMProperty) $parentProperty = $arg1;
		}
		if (func_num_args() == 3) {
			$arg1 = func_get_arg(1);
			if ($arg1 instanceof ReflectionORMClass) $this->subORMClass = $arg1;
			$arg2 = func_get_arg(2);
			//FK
			if ($arg2 instanceof ReflectionORMProperty) $parentProperty = $arg2;
		}

		//Cria os objetos de reflexao para os indices, joins e colecoes
		foreach ( (isset($parentProperty) ? $this->getORMProperties($parentProperty) : $this->getORMProperties())  as $property) {
			if ($property->isTransient()) continue;
			if ($property->isIndex()) {
				//Indice FK
				if (class_exists($property->getType())  && in_array( $property->getType(), get_declared_classes() )) {
					
					$fkRf = new ReflectionORMClass($property->getType(),$property);
					$fkIndexes = $fkRf->getIndexes();
					if ($property->isComposite()) {
						//Override das colunas
						$cpColumns = $property->getCompositeColumns();
						for ($i = 0; $i < sizeof($cpColumns); $i++) {
							$fkIndexes[$i]->__overrideAnnotation("Column",$cpColumns[$i]);
						}
					} else {
						//Defaults
						$fkIndexes[0]->__overrideAnnotation("Column",$property->getColumn());
					}
					$property->__setForeignIndexORMProperties($fkIndexes);
					$this->properties[$property->getName()] = $property;
				}
				$this->indexes[] = $property;
			}
			if ($property->isJoined()) {
				$this->joins[] = $property;
				if ($property->getMappedBy()) {
					$this->addMapping($property);
				}
			}
			if ($property->isCollection()) {
				$this->collections[] = $property;
				if ($property->getMappedBy()) {
					$this->addMapping($property);
				}
			}
			if ($property->isColumn() && !$property->isTransient()) $this->columns[] = $property;
		}

		//Cria objeto de reflexao da superclasse
		if ($this->getParentClass()) {
			//Subclasses nao podem possuir indices
			if (sizeof($this->indexes) > 0) throw new ReflectionException("[ORM] A classe [{$this->getName()}] nao deve possuir indices definidos por ser subclasse em uma especializacao. Remova a(s) propriedade(s) anotada(s) com @Id e utilize o mesmo nome de coluna para chave primaria da classe pai para chave estrangeira ou a anotacao @JoinColumn para definir a coluna onde sera gravado o valor da chave estrangeira.");
			//Se possuir superclasse instancia o objeto de reflexao
			$this->parentORMClass = new ReflectionORMClass($this->getParentClass()->getName(),$this);
		} else {
			//Testa indices
			if (sizeof($this->indexes) == 0) throw new ReflectionException("[ORM] A classe [{$this->getName()}] nao possui indices definidos");
		}
	}

	/** Retorna o nome da tabela da classe
	 * @return string
	 */
	public function getTableName() {
		if ($this->getInheritanceStrategy() == InheritanceType::SINGLE_TABLE) {
			if ($this->getParentORMClass()) return $this->getParentORMClass()->getTableName();
		} else if ($this->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
			return $this->hasAnnotation('Table') ? $this->getAnnotation('Table')->getName() : $this->getName();
		} else if ($this->getInheritanceStrategy() == InheritanceType::TABLE_PER_SUBCLASS) {
			if ($this->getSubORMClass()) return $this->getSubORMClass()->getTableName();
		}
		return $this->hasAnnotation('Table') ? $this->getAnnotation('Table')->getName() : $this->getName();
	}

	/** Retorna a objeto de reflexao da superclasse
	 * @return ReflectionORMClass
	 */
	public function getParentORMClass() {
		return $this->parentORMClass;
	}

	/** Retorna a objeto de reflexao da subclasse
	 * @return ReflectionORMClass
	 */
	public function getSubORMClass() {
		return $this->subORMClass;
	}

	/** Retorna a estrategia de heranca utilizada pela classe
	 * @return string
	 */
	public function getInheritanceStrategy() {
		if ($this->getParentORMClass()) {
			return $this->getParentORMClass()->getInheritanceStrategy();
		} else {
			//return $this->inheritanceStrategy;
			if ($this->hasAnnotation('Inheritance') || $this->getSubORMClass()) {
				if ($this->hasAnnotation('Inheritance')) {
					return $this->getAnnotation('Inheritance')->getType();
				} else {
					return InheritanceType::TABLE_PER_CLASS;
				}
			} else {
				return false;
			}
		}
	}

	/** Adiciona uma propriedade mapeada
	 * @param ReflectionORMProperty $property    Propriedade mapeante
	 * @return void
	 */
	private function addMapping(ReflectionORMProperty $property) {
		$this->mappedClassProperties[($property->getType() == "Collection" ? $property->getTargetEntity() : $property->getType())."|".($property->getMappedBy() instanceof ColumnAnnotation ? $property->getMappedBy()->getName() : $property->getMappedBy())] = $property->getName();
	}

	/** Verifica se a classe mapeia a Classe.propriedade
	 * @param string $className    Nome da Classe
	 * @param string $propertyName    Nome da propriedade
	 * @return boolean
	 */
	public function isMapping($className, $propertyName) {
		return isset($this->mappedClassProperties[$className."|".$propertyName]);
	}

	/** Retorna o nome da propriedade mapeando a propriedade da classe informados.
	 * @param string $className   Nome da Classe
	 * @param string $propertyName   Nome da propriedade
	 * @return string
	 */
	public function getMapped($className, $propertyName) {
		if ($this->isMapping($className, $propertyName)) {
			return $this->mappedClassProperties[$className."|".$propertyName];
		} else {
			throw new ORMException("[ORM] A propriedade [".$this->getName().".".$propertyName."] nao mapeia a classe [".$className."]");
		}
	}

	/** Recupera uma ReflectionProperty com informacoes ORM
	 * @param string $name Nome da propriedade
	 * @return ReflectionORMProperty
	 */
	public function getORMProperty($name) {
		if ($this->hasProperty($name)) {
			if (isset($this->properties[$name])) {
				return $this->properties[$name];
			} else {
				try {
					if (func_num_args() == 2) {
						$parentProperty = func_get_arg(1);
						$newProperty = new ReflectionORMProperty($this->getName(),$name,$this,$parentProperty);
					} else {
						$newProperty = new ReflectionORMProperty($this->getName(),$name,$this);
					}
					$this->properties[$name] = $newProperty;
					return $newProperty;
				} catch (Exception $e) {
					throw new ReflectionException("[ORM] Problemas instanciando a classe de Reflexao para a propriedade [".$this->getName().".".$name."].\n".$e->getMessage());
				}
			}
		} else {
			throw new ReflectionException("[ORM] Problemas instanciando a classe de Reflexao para a propriedade [".$this->getName().".".$name."]. A propriedade nao existe.");
		}
	}

	/** Recupera as Classes de Propriedade da entidade com suasinformacoes de ORM
	 * @return array
	 */
	public function getORMProperties() {
		$arr = array();
		$props = $this->getProperties();
		foreach ($props as $property) {
			//$arr[] = new ReflectionORMProperty($this->getName(),$property->getName(),$this);
			if (func_num_args() == 1) {
				$parentProperty = func_get_arg(0);
				$arr[] = $this->getORMProperty($property->getName(),$parentProperty);
			} else {
				$arr[] = $this->getORMProperty($property->getName());
			}
		}
		return $arr;
	}

	public function getIndexes() {
		if ($this->getParentClass() && $this->getInheritanceStrategy() != InheritanceType::TABLE_PER_CLASS) {
			return array_merge($this->getParentORMClass()->getIndexes(),$this->indexes);
		} else {
			return $this->indexes;
		}
		//return $this->indexes;
	}

	public function getJoins() {
		if ($this->getParentClass() && $this->getInheritanceStrategy() != InheritanceType::TABLE_PER_CLASS) {
			return array_merge($this->getParentORMClass()->getJoins(),$this->joins);
		} else {
			return $this->joins;
		}
		//return $this->joins;
	}

	public function getCollections() {
		if ($this->getParentClass() && $this->getInheritanceStrategy() != InheritanceType::TABLE_PER_CLASS) {
			return array_merge($this->getParentORMClass()->getCollections(),$this->collections);
		} else {
			return $this->collections;
		}
		//return $this->collections;
	}

	public function getColumns() {
		if ($this->getParentClass() && $this->getInheritanceStrategy() != InheritanceType::TABLE_PER_CLASS) {
			return array_merge($this->getParentORMClass()->getColumns(),$this->columns);
		} else {
			return $this->columns;
		}
	}
	
	public function isVersioned() {
		return $this->hasAnnotation('Versioning');
	}
	
	public function getVersionColumnName() {
		return $this->getAnnotation('Versioning')->getColumn();
	}
	
	private function __overrideAnnotation($name,Annotation $value) {
		$this->annotations[$name] = $value;
	}
	
	public function __call($name,$arguments) {
		if ($name == "__overrideAnnotation") {
			call_user_func_array(array($this,$name),$arguments);
		} else {
			throw new ReflectionException("[FATAL] Metodo nao encontrado (".$name.")");
		}
	}
}

?>