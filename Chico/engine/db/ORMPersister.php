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
 * File: ORMPersister.php
 **/

import('engine.db.ORM');

/** Classe de gravacao das entidades
 * @author Silas R. N. Junior
 */
class ORMPersister extends ORM {

	/** Estrategia cascade para o mapeamento
	 * @var mixed
	 */
	private $cascade;

	/** ORMPersister da superclasse
	 * @var ORMPersister
	 */
	private $superPersister;

	/** Ultima operaçao executada pelo persister
	 * @var string
	 */
	private $lastOperation;

	/**
	 * @param ReflectionORMClass $rflctORM
	 * @param string $alias    Apelido da entidade
	 * @param string $path    Caminho da entidade na arvore
	 * @param int $depth    Profundidade do relacionamento
	 */
	public function ORMPersister(ReflectionORMClass $rflctORM, $alias, $path, $depth = null) {
		$this->setRflctORM($rflctORM);
		$this->setAlias($alias);
		$this->setPath($path);
		$this->setDepth($depth);
	}

	/** Verifica se o tipo de sincronia e um UPDATE - Esse metodo deve ser usado com cautela pois pode ocasionar uma query
	 * @param DbDriver $driver    Driver do banco de dados
	 * @param object $entity    Entidade
	 * @return boolean
	 */
	public function isUpdate(DbDriver $driver,$entity) {

		//operacao de persistencia em subclasses e definido pela operacao da classe mae
		if ($this->getSuperPersister()) {
			$sp = $this->getSuperPersister();
			while ($sp->getSuperPersister()) {
				$sp = $sp->getSuperPersister();
			}
			if ($sp->getLastOperation() == "UPDATE") {
				return true;
			} else {
				return false;
			}
		}

		$update = false;
		$classMeta = $this->getRflctORM();
		if($classMeta->getParentClass()) {
			$classMeta = $classMeta->getParentORMClass();
		}
		$tmpIdx = array();
		$tmpIdxColumn = array();
		foreach ($classMeta->getIndexes() as $index) {
			if ($index->isForeignKey()) {
				$foreignKeys = $index->getForeignIndexORMProperties();
				foreach ($foreignKeys as $fkIndex) {
					$tmpIdxColumn[] = $driver->formatField($fkIndex->getColumnName());
					$value = trim($fkIndex->getValue($entity));
					if (strlen($value) == 0) throw new ORMException("[Runtime] Erro de indice [".$classMeta->getName().".".$index->getName()."]. O indice nao deve possuir valores nulos");
					$tmpIdx[] = $driver->formatField($fkIndex->getColumnName()) . " = " . $driver->formatValue($fkIndex->getType(),$value);
				}
			} else {
				if ($index->getGenerationStrategy() == GenerationType::AUTO || $index->getGenerationStrategy() == GenerationType::MAX) {
					if (strlen(trim($index->getValue($entity))) > 0) $update  = true;
				} else {
					$tmpIdxColumn[] = $driver->formatField($index->getColumnName());
					$tmpIdx[] = $driver->formatField($index->getColumnName()) . " = " . $driver->formatValue($index->getType(),trim($index->getValue($entity)));
				}
			}
		}
		if (sizeof($tmpIdx) > 0) {
			$tmpIdxTable = $driver->formatTable($classMeta->getTableName());
			if (sizeof($driver->fetchAssoc("SELECT ".implode(",",$tmpIdxColumn)." FROM ".$tmpIdxTable." WHERE ".implode(" AND ",$tmpIdx))) > 0) $update  = true;
		}
		return $update;
	}

	/** Retorna a estrategia de propagacao das operacoes de persistencia
	 * @return mixed
	 */
	public function getCascade() {
		return $this->cascade;
	}

	/** Define a estrategia de propagacao das operacoes de persistencia
	 * @param mixed $newCascade
	 * @return void
	 */
	public function setCascade($newCascade) {
		$this->cascade = $newCascade;
	}

	/** Constroi a string SQL
	 * @param DbDriver $driver    Driver do banco de dados
	 * @param object $entity    Objeto da entidade
	 * @return void
	 */
	public function buildSQL(ORMRequest $request) {
		
		$driver = $request->getDriver();
		$entity = $request->getEntity();

		$currentEntity = $this->fetchEntity($entity);
		if (!$currentEntity) return;

		//Cache
		if ($this->getCached()) {
			$sqlArr = $this->getCached();
		} else {
			$sqlArr = array();
		}

		if ($this->isUpdate($driver,$currentEntity) &&
		!(($this->getCascade() == CascadeType::NONE) ||
		($this->getCascade() == CascadeType::DELETE) ||
		($this->getCascade() == CascadeType::CREATE))) {
			$this->setLastOperation("UPDATE");
			if (!isset($sqlArr['update'])) {

				$pairs = array();
				$sqlArr['update'] = "UPDATE ".$driver->formatTable($this->getRflctORM()->getTableName()). " SET ";
				foreach ($this->getRflctORM()->getColumns() as $column) {
					if ($column->isTransient() || $column->isIndex()) continue;
					$pairs[] = $driver->formatField($column->getColumnName())." = ?";
				}
				//Joins
				foreach ($this->getRflctORM()->getJoins() as $join) {
					if ($join->isTransient() || $join->isIndex()) continue;
					if ($join->getMappedBy()) {
						// Nesse caso o objeto que referencia sera persistido depois
					} else {
						$pairs[] = $driver->formatField($join->getColumnName())." = ?";
					}
				}
				
				//Heranca
				if ($this->getRflctORM()->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
					$parent = $this->getRflctORM()->getParentORMClass();
					if ($this->getRflctORM()->hasAnnotation("JoinColumn")) {
						$where[] = $driver->formatField($this->getRflctORM()->getAnnotation("JoinColumn")->getName())." = ?";
					} else {
						foreach ($parent->getIndexes() as $index) {
							$where[] = $driver->formatField($index->getColumnName())." = ?";
						}
					}
				}
				
				//Versioning (propriedade)
				if ($this->getRflctORM()->isVersioned()) {
					//Atualiza a versao
					$pairs[] = $driver->formatField($this->getRflctORM()->getVersionColumnName())." = ?";
				}
				//Versioning (where versao anterior)
				if ($this->getRflctORM()->isVersioned()) {
					//Somente se encontrar a versao correta
					$where[] = $driver->formatField($this->getRflctORM()->getVersionColumnName())." = ?";
				}
				
				//indices
				$indexes = $this->getRflctORM()->getIndexes();
				foreach ($indexes as $index) {
					$where[] = $driver->formatField($index->getColumnName())." = ?";
				}
				
				
				$sqlArr['update'] .= implode(",",$pairs)." WHERE (".implode(" AND ",$where).")";
				$this->setCached($sqlArr);
			}
			$sql = $sqlArr['update'];

		} else if (!(($this->getCascade() == CascadeType::NONE) ||
		($this->getCascade() == CascadeType::DELETE) ||
		($this->getCascade() == CascadeType::UPDATE))) {
			$this->setLastOperation("INSERT");
			if (!isset($sqlArr['insert'])) {
				$values = array();
				$columns = array();

				$sqlArr['insert'] = "INSERT INTO ".$driver->formatTable($this->getRflctORM()->getTableName());
				foreach ($this->getRflctORM()->getColumns() as $column) {
					if ($column->isTransient() || $column->isIndex()) continue;

					$columns[] = $driver->formatField($column->getColumnName());
					$values[] = "?";
				}

				//Joins
				foreach ($this->getRflctORM()->getJoins() as $join) {
					if ($join->isTransient() || $join->isIndex()) continue;
					if ($join->getMappedBy()) {
						// Nesse caso o objeto que referencia sera persistido depois
					} else {
						$columns[] = $driver->formatField($join->getColumnName());
						$values[] = "?";
					}
				}
				
				//Heranca
				if ($this->getRflctORM()->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
					$parent = $this->getRflctORM()->getParentORMClass();
					if ($this->getRflctORM()->hasAnnotation("JoinColumn")) {
						$columns[] = $driver->formatField($this->getRflctORM()->getAnnotation("JoinColumn")->getName());
						$values[] = "?";
					} else {
						foreach ($parent->getIndexes() as $index) {
							$columns[] = $driver->formatField($index->getColumnName());
							$values[] = "?";
						}
					}
				}
				
				//Versioning
				if ($this->getRflctORM()->isVersioned()) {
					$columns[] = $driver->formatField($this->getRflctORM()->getVersionColumnName());
					$values[] = "?";
				}

				//Indices
				foreach ($this->getRflctORM()->getIndexes() as $index) {
					if ($index->isForeignKey()) {
						$foreignKeys = $index->getForeignIndexORMProperties();
						foreach ($foreignKeys as $fkIndex) {
							$columns[] = $driver->formatField($fkIndex->getColumnName());
							$values[] = $driver->formatValue($fkIndex->getType(),trim($fkIndex->getValue($entity)));
						}
					} else {
						//Caso Estrategia AUTO pular
						if ($index->getGenerationStrategy() == GenerationType::AUTO) continue;
						//Coluna
						$columns[] = $driver->formatField($index->getColumnName());
						//Caso MAX obter valor
						if ($index->getGenerationStrategy() == GenerationType::MAX) {
							$maxValue = $driver->fetchAssoc("SELECT MAX(".$driver->formatField($index->getColumnName()).") AS ".$index->getColumnName()." FROM ".$driver->formatTable($this->getRflctORM()->getTableName()));
							$maxValue = (isset($maxValue[0][$index->getColumnName()]) ? $maxValue[0][$index->getColumnName()] + 1 : 1);
							$values[] = $maxValue;
							$index->setValue($currentEntity, $driver->unformatValue($index->getType(),$maxValue));
						} else {
							$values[] = "?";
						}
					}
				}

				$sqlArr['insert'] .= " (".implode(",",$columns).") VALUES (".implode(",",$values).")";
				$this->setCached($sqlArr);
			}
			$sql = $sqlArr['insert'];
		}
		return $sql;
	}

	/** Retorna o persister da superclasse
	 * @return ORMPersister
	 */
	public function getSuperPersister() {
		return $this->superPersister;
	}

	/** Define o persister da superclasse
	 * @param ORMPersister $newSuperPersister
	 * @return void
	 */
	public function setSuperPersister(ORMPersister $newSuperPersister) {
		$this->superPersister = $newSuperPersister;
	}

	/** Retorna a ultima operacao (INSERT ou UPDATE) realizada pelo persister
	 * @return string
	 */
	public function getLastOperation() {
		return $this->lastOperation;
	}

	/**
	 * @param string $newLastOperation
	 * @return void
	 */
	private function setLastOperation($newLastOperation) {
		$this->lastOperation = $newLastOperation;
	}

	/** Grava as colecoes
	 * @param DbDriver $driver    Driver do Banco de dados
	 * @param object $entity    Entidade a ser sincronizada
	 * @return void
	 */
	public function persistCollections(ORMRequest $request) {

		$driver = $request->getDriver();
		$entity = $request->getEntity();

		$currentLoader = $this;
		while($currentLoader) {
			$currentEntity = $currentLoader->fetchEntity($entity);

			foreach ($currentLoader->getRflctORM()->getCollections() as $collection) {
					
					
				//Por que desse persister? pra evitar que a operacao de build ordene usando esse nodo (owner da colecao) novamente
				$persisterBuilder = new ORMPersisterBuilder();
				$persisterBuilder->build($collection->getTargetEntity(),null,$collection->getName(),$currentLoader,$currentLoader->getDepth());
				$persister = $persisterBuilder->getRoot();
					
				$deleterBuilder = new ORMDeleterBuilder();
				$deleterBuilder->build($collection->getTargetEntity(),null,$collection->getName(),$currentLoader,$currentLoader->getDepth());
				$deleter = $deleterBuilder->getRoot();

				$collectionRflctORM = EntityManager::getReflectionData($collection->getTargetEntity());
				$collectionInstance = (is_null($collection->getValue($currentEntity)) ? new Collection(array()) : $collection->getValue($currentEntity));

				//Adicionados
				$itemsToAdd = $collectionInstance->getAdded();
				//Modificados
				//$itemsToUpdate = $collectionInstance->getModified();
				//Removidos
				$itemsToRemove = $collectionInstance->getRemoved();
					
				if ($collection->isOneToMany()) {

					//Cascade?
					$isCascade = false;
					if ($currentLoader->getLastOperation() == "UPDATE") {
						//UPDATE
						if (($collection->getCascade() == CascadeType::ALL)||
						($collection->getCascade() == CascadeType::SAVE)||
						($collection->getCascade() == CascadeType::UPDATE)) {
							$isCascade = true;
						}
					} else {
						//INSERT
						if (($collection->getCascade() == CascadeType::ALL)||
						($collection->getCascade() == CascadeType::SAVE)||
						($collection->getCascade() == CascadeType::CREATE)) {
							$isCascade = true;
						}
					}

					$mappedProperty = $collectionRflctORM->getORMProperty($collection->getMappedBy());

					if ($isCascade) {
							
						foreach ($collectionInstance->toArray() as $collectionItem) {
							if (is_null($mappedProperty->getValue($collectionItem))) throw new ORMException("[Runtime] Erro na colecao [".$currentLoader->getRflctORM()->getName().".".$collection->getName()."]. Existe um item na colecao que nao tem a referencia da entidade que o possui");
							
							if ($request->hasProcessed($collectionItem)) continue;
							$persister->sync($request->getSubRequest($collectionItem));
						}
							
						if (sizeof($itemsToRemove) > 0) {
							//Caso seja delete orphan, remova a entidade por completo
							if ($collection->isDeleteOrphan()) {
								$deleter->sync($driver,$itemsToRemove);
									
								//caso nao, remova o link
							} else {
								foreach ($itemsToRemove as $removedItem) {
									$mappedProperty->setValue($removedItem,null);
								}
								$persister->sync($request->getSubRequest($itemsToRemove));
							}
						}
					} else {
						//Atualiza links - nao temos um cascade mas precisamos atualizar os links
							
						//Prepara variaveis
						if ($mappedProperty->isIndex()) {
							$collectionIndexes = $mappedProperty->getForeignIndexORMProperties();
							$mappedProperties = $mappedProperty->getForeignIndexORMProperties();
						} else {
							$collectionIndexes = $collectionRflctORM->getIndexes();
							if ($mappedProperty->isComposite()) {
								$mappedProperties = $mappedProperty->getCompositeColumns();
							} else {
								$mappedProperties = array($mappedProperty);
							}
						}
						$entityIndexes = $currentLoader->getRflctORM()->getIndexes();
							
						if (sizeof($collectionIndexes) != sizeof($entityIndexes)) throw new ORMException("[ORM] Erro no relacionamento [".$currentLoader->getRflctORM()->getName().".".$collection->getName()."]. Quantidade incorreta de indices mapeados.");
							

						foreach ($itemsToAdd as $current) {

							if (is_null($mappedProperty->getValue($current))) throw new ORMException("[Runtime] Erro na colecao [".$currentLoader->getRflctORM()->getName().".".$collection->getName()."]. Existe um item na colecao que nao tem a referencia da entidade que a possui");
							$fields = array();
							$where = array();
							for ($i = 0; $i < sizeof($collectionIndexes); $i++) {

								if (strlen(trim($collectionIndexes[$i]->getValue($current))) == 0) {
									throw new EntityNotFoundException("[Runtime] Erro na colecao [".$currentLoader->getRflctORM()->getName().".".$collection->getName()."]. Entidade nao persistente incluida na colecao.");
								}
								$fields[] = $driver->formatField($mappedProperties[$i]->getColumnName())." = ".$driver->formatValue($entityIndexes[$i]->getType(),$entityIndexes[$i]->getValue($currentEntity));
								$where[] = $driver->formatField($collectionIndexes[$i]->getColumnName())." = ".$driver->formatValue($collectionIndexes[$i]->getType(),$collectionIndexes[$i]->getValue($current));
							}
							$sql = "UPDATE {$driver->formatTable($collectionRflctORM->getTableName())} SET ".implode(",",$fields)." WHERE (".implode(" AND ",$where).")";
							$driver->run($sql);
						}
						foreach ($itemsToRemove as $current) {

							if (is_null($mappedProperty->getValue($current))) throw new ORMException("[Runtime] Erro na colecao [".$currentLoader->getRflctORM()->getName().".".$collection->getName()."]. Existe um item na colecao que nao tem a referencia da entidade que a possui");
							$fields = array();
							$where = array();
							for ($i = 0; $i < sizeof($collectionIndexes); $i++) {

								if (strlen(trim($collectionIndexes[$i]->getValue($current))) == 0) {
									throw new EntityNotFoundException("[Runtime] Erro na colecao [".$currentLoader->getRflctORM()->getName().".".$collection->getName()."]. Entidade nao persistente incluida na colecao.");
								}
								$fields[] = $driver->formatField($mappedProperties[$i]->getColumnName())." = NULL";
								$where[] = $driver->formatField($collectionIndexes[$i]->getColumnName())." = ".$driver->formatValue($collectionIndexes[$i]->getType(),$collectionIndexes[$i]->getValue($current));
							}
							$sql = "UPDATE {$driver->formatTable($collectionRflctORM->getTableName())} SET ".implode(",",$fields)." WHERE (".implode(" AND ",$where).")";
							$driver->run($sql);
						}
					}


				} else if ($collection->isManyToMany()) {

					$joinTable = $collection->getJoinTable();
					$joinColumns = (is_array($collection->getJoinColumns()) ? $collection->getJoinColumns() :  array($collection->getJoinColumns()));
					$inverseJoinColumns = (is_array($collection->getInverseJoinColumns()) ? $collection->getInverseJoinColumns() :  array($collection->getInverseJoinColumns()));
					$thisIndexes = $this->getRflctORM()->getIndexes();
					$collectionIndexes = $collectionRflctORM->getIndexes();

					//Verificar a bidirecionalidade
					//TODO - Mover isso para classe de Reflexao
					if (!$collection->getMappedBy()) {
						if ($collectionRflctORM->isMapping($this->getRflctORM()->getName(),$collection->getName())) {
							$inverseCollectionRflctORM = $collectionRflctORM->getORMProperty($collectionRflctORM->getMapped($this->getRflctORM()->getName(),$collection->getName()));
						} else {
							//throw new ORMException("Erro na associacao [".$this->getRflctORM()->getName().".".$collection->getName()."]. A colecao nao esta mapeada corretamente na classe ".$collection->getTargetEntity().". O relacionamento deve ser bidirecional e um dos dois lados do relaciomanento deve conter a opcao @ManyToMany(mappedBy=\"<propriedade>\"...) definida");
							//Unidirecional
							$inverseCollectionRflctORM = false;
						}
					} else {
						$inverseCollectionRflctORM = $collectionRflctORM->getORMProperty($collection->getMappedBy());
					}

					//Cascade?
					if ($this->getLastOperation() == "UPDATE") {
						//UPDATE
						if (($collection->getCascade() == CascadeType::ALL)||
						($collection->getCascade() == CascadeType::SAVE)||
						($collection->getCascade() == CascadeType::UPDATE)) {

							//foreach ($itemsToUpdate as $current) {
							foreach ($collectionInstance->toArray() as $current) {
								if ($inverseCollectionRflctORM) {
									$inverseCollectionInstance = $inverseCollectionRflctORM->getValue($current);
									if (is_null($inverseCollectionInstance)) throw new ORMException("[Runtime] Erro de consistencia. O relacionamento bidirecional [".$this->getRflctORM()->getName().".".$collection->getName()."] possui uma instancia de colecao somente em um dos lados do relacionamento");
									if ($inverseCollectionInstance->contains($currentEntity)) {
										$inverseCollectionInstance->resetState($currentEntity);
									} else {
										throw new ORMException("[Runtime] Erro de consistencia. Uma objeto contido na colecao [".$this->getRflctORM()->getName().".".$collection->getName()."] existe somente em um dos lados do relacionamento");
									}
								}
									
								if (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($current))) {
									if (!EntityManager::getCacher()->getCacheData(EntityManager::getCacher()->getOIDString($current))->isDirty()) continue;
								}
								$persister->sync($request->getSubRequest($current));
								$collectionInstance->resetState($current);
							}
						}
					} else {
						//INSERT
						if (($collection->getCascade() == CascadeType::ALL)||
						($collection->getCascade() == CascadeType::SAVE)||
						($collection->getCascade() == CascadeType::CREATE)) {
							foreach ($collectionInstance->toArray() as $current) {
								if ($inverseCollectionRflctORM) {
									$inverseCollectionInstance = $inverseCollectionRflctORM->getValue($current);
									if (is_null($inverseCollectionInstance)) throw new ORMException("[Runtime] Erro de consistencia. O relacionamento bidirecional [".$this->getRflctORM()->getName().".".$collection->getName()."] possui uma instancia de colecao somente em um dos lados do relacionamento");
									if ($inverseCollectionInstance->contains($currentEntity)) {
										$inverseCollectionInstance->resetState($currentEntity);
									} else {
										throw new ORMException("[Runtime] Erro de consistencia. Uma objeto contido na colecao [".$this->getRflctORM()->getName().".".$collection->getName()."] existe somente em um dos lados do relacionamento");
									}
								}

								if (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($current))) {
									if (!EntityManager::getCacher()->getCacheData(EntityManager::getCacher()->getOIDString($current))->isDirty()) continue;
								}
								$persister->sync($request->getSubRequest($current));
								$collectionInstance->resetState($current);
							}
						}
					}

					//TODO - A ordem dos indices deve ser a mesma das join columns ou teremos um problema. Melhorar isso
					foreach ($itemsToAdd as $current) {
						$fields = array();
						$values = array();
						$sql = "INSERT INTO ".$driver->formatTable($joinTable);
							
						//Owner Side
						for ($i = 0; $i < sizeof($joinColumns); $i++) {
							$fields[] = $driver->formatField($joinColumns[$i]);
							$values[] = $driver->formatValue($thisIndexes[$i]->getType(),$thisIndexes[$i]->getValue($currentEntity));
						}
							
						//Owned Side
						for ($i = 0; $i < sizeof($inverseJoinColumns); $i++) {
							$fields[] = $driver->formatField($inverseJoinColumns[$i]);
							if (strlen(trim($collectionIndexes[$i]->getValue($current))) == 0) throw new ORMException("[Runtime] Uma entidade na colecao ".$this->getRflctORM()->getName().".".$collection->getName()." nao foi persistida anteriormente. Verifique se o relaciomento define uma estrategia de CASCADE adequada, se a entidade foi persistida antes de ser inclusa na colecao ou se ela foi carregada no caso de ja ser persistente.");
							$values[] = $driver->formatValue($collectionIndexes[$i]->getType(),$collectionIndexes[$i]->getValue($current));
						}
							
						$sql = $sql." (".implode(",",$fields).") VALUES (".implode(",",$values).")";
						$driver->run($sql);
					}

					foreach ($itemsToRemove as $current) {
						$values = array();
						$sql = "DELETE FROM ".$driver->formatTable($joinTable);
							
						//Owner Side
						for ($i = 0; $i < sizeof($joinColumns); $i++) {
							$fields[] = $driver->formatField($joinColumns[$i])." = ".$driver->formatValue($thisIndexes[$i]->getType(),$thisIndexes[$i]->getValue($currentEntity));
						}
							
						//Owned Side
						for ($i = 0; $i < sizeof($inverseJoinColumns); $i++) {
							$fields[] = $driver->formatField($inverseJoinColumns[$i])." = ".$driver->formatValue($collectionIndexes[$i]->getType(),$collectionIndexes[$i]->getValue($current));
						}
							
						$sql = $sql." WHERE ".implode(" AND ",$fields);
						$driver->run($sql);
						$collectionInstance->resetState($current);
					}
				}
			}
			$currentLoader = $currentLoader->getNext();
		}
	}

	private function fillSQL (ORMRequest $request,$sql) {

		$driver = $request->getDriver();
		$entity = $request->getEntity();

		$currentEntity = $this->fetchEntity($entity);
		if (!$currentEntity) return;
		//Preenchendo

		//Colunas
		$pos = 0;
		foreach ($this->getRflctORM()->getColumns() as $column) {
			if ($column->isTransient() || $column->isIndex()) continue;
			$pos = strpos($sql, "?", $pos);
			if ($pos === false) break;
			if ((class_exists($column->getType())  && in_array( $column->getType(), get_declared_classes() )) && is_subclass_of($column->getType(),"Enumeration")) {
				$value = ( is_null($column->getValue($currentEntity)) ? "NULL" : $column->getValue($currentEntity)->ordinal());
			} else {
				$value = ( is_null($column->getValue($currentEntity)) ? "NULL" : $driver->formatValue($column->getType(),$column->getValue($currentEntity)));
			}
			$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
			$pos += strlen($value);
		}

		//Joins
		foreach ($this->getRflctORM()->getJoins() as $join) {
			if ($join->isTransient() || $join->isIndex()) continue;
			if ($join->getMappedBy()) {
				// Nesse caso o objeto que detem a referencia sera persistido depois [ordenado pelo builder]
			} else {
				if ($join->isTransient()) continue;

				if ($join->getValue($currentEntity)) {

					if ($join->isManyToOne()) {
						//ManyToOne - verificar se objeto esta na colecao
						$mappingPropertyName = EntityManager::getReflectionData($join->getType())->getMapped($this->getRflctORM()->getName(),$join->getName());
						$mappingProperty = EntityManager::getReflectionData($join->getType())->getORMProperty($mappingPropertyName);
						$collectionInstance = $mappingProperty->getValue($join->getValue($currentEntity));

						if (is_null($collectionInstance)) throw new Exception("[Runtime] Um Objeto de classe [{$this->getRflctORM()->getName()}] nao esta contido na propriedade colecao [{$join->getType()}.{$mappingPropertyName}] a qual sua propriedade [{$join->getName()}] se refere.");
						if (!($collectionInstance instanceof Collection)) throw new Exception("[Runtime] Um Objeto contido na propriedade colecao [{$join->getType()}.{$mappingPropertyName}] nao e uma colecao.");
						if (!$collectionInstance->contains($currentEntity)) {
							$wasExcluded = false;
							if (!is_null($collectionInstance)) {
								foreach ($collectionInstance->getRemoved() as $removedItem) {
									if (spl_object_hash($currentEntity) == spl_object_hash($removedItem)) {
										$collectionInstance->resetState($currentEntity);
										$wasExcluded = true;
										//Matar objeto do lado many senao ele nao será removido de fato da colecao
										$join->setValue($currentEntity,null);
									}
								}
							}
							if (!$wasExcluded) {
								throw new Exception("[Runtime] Um Objeto de classe [{$this->getRflctORM()->getName()}] nao esta contido na propriedade colecao [{$join->getType()}.{$mappingPropertyName}] a qual sua propriedade [{$join->getName()}] se refere.");
							}
						}
						$collectionInstance->resetState($currentEntity);
					}

					//TODO - Indice composto
					//Objeto removido sera null nesse ponto
					$one = $join->getValue($currentEntity);
					
					if (!$one) {
						//Foi removido
						//TODO - Indice composto
						$rf = EntityManager::getReflectionData($join->getType());
						
						//Pegar o indice da heranca
						if ($rf->getParentClass()) {
							while ($rf->getParentClass()) {
								$rf = $rf->getParentORMClass();
							}
						}
						foreach ($rf->getIndexes() as $index) {
							$pos = strpos($sql, "?", $pos);
							if ($pos === false) break;
							$value = "NULL";
							$sql = substr($sql, 0, $pos) ."NULL". substr($sql, $pos + 1);
							$pos += strlen($value);
						}
					} else {
						//Nao foi removido
						if (!is_object($one)) {
							throw new Exception("[Runtime] Tipo de dado incorreto em [".$this->getRflctORM()->getName().".".$join->getName()."] Esperado:".$join->getType().", obtido:".gettype($one)."(".$one.")");
						}
						//TODO - Indice composto
						$className = get_class($one);
						
						//Proxy?
						if (strstr($className,"LazyFetchProxy")) {
							$one = $one->getSubject();
							$className = str_replace("LazyFetchProxy", "", $className);
						}
						$rf = EntityManager::getReflectionData($className);
						
						//Pegar o indice da heranca
						if ($rf->getParentClass()) {
							while ($rf->getParentClass()) {
								$rf = $rf->getParentORMClass();
							}
						}
						foreach ($rf->getIndexes() as $index) {
							$pos = strpos($sql, "?", $pos);
							if ($pos === false) break;
							$value = $driver->formatValue($index->getType(),$index->getValue($one));
							$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
							$pos += strlen($value);
						}
					}
				} else {
					//TODO - Indice composto
					$rf = EntityManager::getReflectionData($join->getType());
					//Pegar o indice da heranca
					if ($rf->getParentClass()) {
						while ($rf->getParentClass()) {
							$rf = $rf->getParentORMClass();
						}
					}
					
					foreach ($rf->getIndexes() as $index) {
						$pos = strpos($sql, "?", $pos);
						if ($pos === false) break;
						$value = "NULL";
						$sql = substr($sql, 0, $pos) ."NULL". substr($sql, $pos + 1);
						$pos += strlen($value);
					}
				}
			}
		}
		
		//Heranca
		if ($this->getRflctORM()->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
			$parent = $this->getRflctORM()->getParentORMClass();
			foreach ($parent->getIndexes() as $index) {
				$pos = strpos($sql, "?", $pos);
				if ($pos === false) break;
				$value = ( is_null($index->getValue($currentEntity)) ? "NULL" : $driver->formatValue($index->getType(),$index->getValue($currentEntity)));
				$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
				$pos += strlen($value);
			}
		}
		
		//Versioning
		if ($this->getRflctORM()->isVersioned()) {
			if (stristr($sql,"INSERT")) {
				//Insert
				
				$pos = strpos($sql, "?", $pos);
				$value = 1;
				$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
				$pos += strlen($value);
				
			} else if (stristr($sql,"UPDATE")) {
				//Update
				if (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($currentEntity))) {
					$currentVersion = EntityManager::getCacher()->getVersion(EntityManager::getCacher()->getOIDString($currentEntity)); 
				}
				if (!$currentVersion) {
					throw new ORMException("[Runtime] Erro de versionamento persistindo objeto de classe [".$this->getRflctORM()->getName()."]. Objetos marcados como @Versioning devem ser carregados (load) para que possam ser atualizados.");
				} else {
					//Novo valor
					$pos = strpos($sql, "?", $pos);
					$value = $currentVersion + 1;
					$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
					$pos += strlen($value);
					
					//Where = valor antigo
					$pos = strpos($sql, "?", $pos);
					//if ($pos === false) break;
					$value = $currentVersion;
					$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
					$pos += strlen($value);
				}
			}
		}

		//indices
		//$pos = 0;
		foreach ($this->getRflctORM()->getIndexes() as $index) {
			if ($index->isForeignKey()) {
				$foreignKeys = $index->getForeignIndexORMProperties();
				foreach ($foreignKeys as $fkIndex) {
					//$columns[] = $driver->formatField($fkIndex->getColumnName());
					//$values[] = $driver->formatValue($fkIndex->getType(),trim($fkIndex->getValue($entity)));
					$pos = strpos($sql, "?", $pos);
					if ($pos === false) break;
					$value = $driver->formatValue($fkIndex->getType(),trim($fkIndex->getValue($entity)));
					$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
					$pos += strlen($value);
				}
			} else {
				$pos = strpos($sql, "?", $pos);
				if ($pos === false) break;
				$value = $driver->formatValue($index->getType(),$index->getValue($currentEntity));
				$sql = substr($sql, 0, $pos) .$value. substr($sql, $pos + 1);
				$pos += strlen($value);
			}
		}
		return $sql;
	}

	/** Executa a sincronia da(s) entidade(as) com o banco de dados
	 * @param DbDriver $driver    Driver do banco de dados
	 * @param object $entity   Entidade a ser sincronizada
	 * @return void
	 */
	public function sync(ORMRequest $request) {

		$driver = $request->getDriver();
		$entity = $request->getEntity();

		$current = $this;
		while($current) {
			$currentEntity = $current->fetchEntity($entity);
			//Nulo? Ja processado? Continue
			if (!$current->getRflctORM()->getParentClass() && (is_null($currentEntity) || $request->hasProcessed($currentEntity))) {
				$current = $current->getNext();
				continue;
			}
			$sql = $current->buildSQL($request);
			$preparedSQL = $current->fillSQL($request,$sql);

			//Update?
			if ($current->getLastOperation() == "UPDATE") {
				//Check cache [antes de executar a query]
				If (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($currentEntity))) {
					if (spl_object_hash($currentEntity) != spl_object_hash(EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($currentEntity)))) {
						throw new Exception("[Runtime] Existe uma inconsistencia com a instancia da entidade de classe ".get_class($currentEntity)." existe mais de uma instancia de mesmo ID e classe. A Entidade deve ter uma unica instancia para cada ID");
					}
				}
			}
			try {
				$driver->run($preparedSQL);
			} catch (NoRecordsAffectedException $e) {
				if ($current->getRflctORM()->isVersioned()) {
					//select na tora
					$sql = "SELECT ".
					$current->getAlias().".".$driver->formatField($this->getRflctORM()->getVersionColumnName())." AS ".$this->getRflctORM()->getVersionColumnName().
					" FROM ".$driver->formatTable($current->getRflctORM()->getTableName())." ".$current->getAlias();
					//indices
					$indexes = $current->getRflctORM()->getIndexes();
					foreach ($indexes as $index) {
						if ($index->isForeignKey()) {
							$foreignKeys = $index->getForeignIndexORMProperties();
							foreach ($foreignKeys as $fkIndex) {
								$where[] = $current->getAlias().".".$driver->formatField($fkIndex->getColumnName())." = ".$driver->formatValue($index->getType(),$fkIndex->getValue($currentEntity));;
							}
						} else {
							$where[] = $current->getAlias().".".$driver->formatField($index->getColumnName())." = ".$driver->formatValue($index->getType(),$index->getValue($currentEntity));;
						}
					}
					$sql .= " WHERE (".implode(" AND ",$where).")";
					$result = $driver->fetchAssoc($sql);
					if (sizeof($result) > 0) {
						$currentVersion = EntityManager::getCacher()->getVersion(EntityManager::getCacher()->getOIDString($currentEntity));
						throw new VersioningException("[Runtime] Erro gravando objeto de classe [".$current->getRflctORM()->getName()."]. Versao no cache [".$currentVersion."], versão no banco [".$result[0][$this->getRflctORM()->getVersionColumnName()]."].");
					}
				}
			}
			//Insert?
			if ($current->getLastOperation() == "INSERT") {
				//Se heranca setar so id da classe mae
				if(!$current->getRflctORM()->getParentClass() ||
				$current->getRflctORM()->getInheritanceStrategy() != InheritanceType::TABLE_PER_CLASS ||
				($current->getRflctORM()->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS &&
				$current->getRflctORM()->getName() == $current->getRflctORM()->getParentORMClass()->getName())) {
					//Setar ID - TODO - Verificar estrategias de geracao
					foreach ($current->getRflctORM()->getIndexes() as $index) {
						if ($index->isForeignKey()) {
							continue;
						} else {
							if ($index->getGenerationStrategy() != GenerationType::AUTO) continue;
							$index->setValue($currentEntity, $driver->unformatValue($index->getType(),$driver->getLastInserted($index)));
						}
					}
					//Cache it
					EntityManager::getCacher()->cache(EntityManager::getCacher()->getOIDString($currentEntity),$currentEntity);
					$request->setProcessed($currentEntity);
				}
			}
			
			//Versioning - atualizar a versao
			if ($current->getRflctORM()->isVersioned()) {
				$currentVersion = EntityManager::getCacher()->getVersion(EntityManager::getCacher()->getOIDString($currentEntity));
				EntityManager::getCacher()->setVersion(EntityManager::getCacher()->getOIDString($currentEntity),$currentVersion + 1);
			}
			
			//Atualizar referencias para maximizar eficiencia 
			foreach ($current->getRflctORM()->getJoins() as $join) {
				//if ($join->isIndex()) continue;
				
				$joinEntity = $join->getValue($currentEntity);
				$joinRflctORM = EntityManager::getReflectionData($join->getType());
				
				if (isset($joinEntity)) {
					if ($join->getMappedBy()) {
						$mappedProperty = $joinRflctORM->getORMProperty($join->getMappedBy());
					} else {
						if ($joinRflctORM->isMapping($current->getRflctORM()->getName(), $join->getName())) {
							$mappedProperty = $joinRflctORM->getORMProperty($joinRflctORM->getMapped($current->getRflctORM()->getName(), $join->getName()));
						}
					}
					
					if ($join->isOneToOne()) {
						if (isset($mappedProperty)) {
							//$mappedProperty->setValue($joinEntity, null);
						}
					} else if ($join->isManyToOne()) {
						if (isset($mappedProperty)) {
							$joinedEntityCollection = $mappedProperty->getValue($joinEntity);
							if (isset($joinedEntityCollection) && $joinedEntityCollection->contains($currentEntity)) {
								$joinedEntityCollection->resetState($currentEntity);
								//echo "Resetado\n";
							}
						} 
					}
					//$join->setValue($currentEntity,null);
				}
			}
			
			unset($currentEntity);
			$current = $current->getNext();
		}

		//Collections
		$this->persistCollections($request);
	}
	
	public function rollback(ORMRequest $request) {
		
		if(defined('ENGINE_DEBUG_LOG')) {
			if(defined('ENGINE_DEBUG_VERBOSE')) {
				if (ENGINE_DEBUG_VERBOSE > 5) {
					Logger::getInstance()->add("Cache Rollback");
				}
			}
		}
		
		$driver = $request->getDriver();
		$entity = $request->getEntity();
		
		$current = $this;
		while($current) {
			$currentEntity = $current->fetchEntity($entity);
			
			//Mata o cache
			$cachedEntity = EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($currentEntity));
			if ($cachedEntity) {
				EntityManager::getCacher()->remove(EntityManager::getCacher()->getOIDString($currentEntity));
				if(defined('ENGINE_DEBUG_LOG')) {
					if(defined('ENGINE_DEBUG_VERBOSE')) {
						if (ENGINE_DEBUG_VERBOSE > 5) {
							Logger::getInstance()->add("Rollback: ".EntityManager::getCacher()->getOIDString($currentEntity));
						}
					}
				}
			}
			$current = $current->getNext();
		}
		
		/*
		// e da reload
		try {
			if(defined('ENGINE_DEBUG_LOG')) {
				if(defined('ENGINE_DEBUG_VERBOSE')) {
					if (ENGINE_DEBUG_VERBOSE > 5) {
						Logger::getInstance()->add("Reload Cache");
					}
				}
			}
			$builder = new ORMLoaderBuilder(true);
			$builder->build($this->getRflctORM()->getName());
			$loader = $builder->getRoot();
			$loader->sync($request);
		} catch (Exception $e) {
			die("Erro recarregando cache");
		}
		*/
		
		
		//Collections
		//$this->rollbackCollections($request);
	}
	
	public function rollbackCollections(ORMRequest $request) {
		$driver = $request->getDriver();
		$entity = $request->getEntity();

		$currentLoader = $this;
		while($currentLoader) {
			$currentEntity = $currentLoader->fetchEntity($entity);
			foreach ($currentLoader->getRflctORM()->getCollections() as $collection) {
				$collectionInstance = (is_null($collection->getValue($currentEntity)) ? new Collection(array()) : $collection->getValue($currentEntity));
				foreach ($collectionInstance->toArray() as $collectionItem) {
					$cachedColItem = EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($collectionItem));
					if ($cachedColItem) {
						EntityManager::getCacher()->remove(EntityManager::getCacher()->getOIDString($cachedColItem));
					}
				}
				$collectionInstance->resetState($collection);
			}
			$currentLoader = $currentLoader->getNext();
		}
	}
}

?>
