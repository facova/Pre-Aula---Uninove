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
 * File: ORMDeleter.php
 **/

import('engine.db.ORM');

/** Classe de exclusao das entidades
 * @author Silas R. N. Junior
 */
class ORMDeleter extends ORM {

	/** Estrategia cascade para o mapeamento
	 * @var mixed
	 */
	private $cascade;

	/**
	 * @param ReflectionORMClass $rflctORM
	 * @param string $alias    Apelido da entidade
	 * @param string $path    Caminho da entidade na arvore
	 * @param int $depth    Profundidade do relacionamento
	 */
	public function ORMDeleter(ReflectionORMClass $rflctORM, $alias, $path, $depth = null) {
		$this->setRflctORM($rflctORM);
		$this->setAlias($alias);
		$this->setPath($path);
		$this->setDepth($depth);
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

		if ($this->getCached()) {
			$sql = $this->getCached();
		} else {

			$sql = "DELETE FROM ".$driver->formatTable($this->getRflctORM()->getTableName());
			$current = $this;
		}

		//Heranca
		if ($this->getRflctORM()->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
			$parent = $this->getRflctORM()->getParentORMClass();
			while ($parent->getParentClass()) {
				$parent = $parent->getParentORMClass();
			}
			if ($this->getRflctORM()->hasAnnotation("JoinColumn")) {
				foreach ($parent->getIndexes() as $index) {
					$where[] = $driver->formatField($this->getRflctORM()->getAnnotation("JoinColumn")->getName())." = ".$driver->formatValue($index->getType(),$index->getValue($currentEntity));
				}
			} else {
				foreach ($parent->getIndexes() as $index) {
					$where[] = $driver->formatField($index->getColumnName())." = ".$driver->formatValue($index->getType(),$index->getValue($currentEntity));
				}
			}
		} else {

			//indices
			$indexes = $this->getRflctORM()->getIndexes();
			$where = array();
			foreach ($indexes as $index) {
				if ($index->isForeignKey()) {
					$foreignKeys = $index->getForeignIndexORMProperties();
					foreach ($foreignKeys as $fkIndex) {
						$where[] = $driver->formatField($fkIndex->getColumnName())." = ".$driver->formatValue($fkIndex->getType(),$fkIndex->getValue($currentEntity));
					}
				} else {
					$where[] = $driver->formatField($index->getColumnName())." = ".$driver->formatValue($index->getType(),$index->getValue($currentEntity));
				}
			}
		}

		$sql .= " WHERE (".implode(" AND ",$where).")";
		return $sql;
	}

	/** Remove as colecoes
	 * @param DbDriver $driver    Driver do Banco de dados
	 * @param object $entity    Entidade a ser sincronizada
	 */
	public function deleteCollections(ORMRequest $request) {

		$driver = $request->getDriver();
		$entity = $request->getEntity();

		$currentEntity = $this->fetchEntity($entity);
		if (!$currentEntity) return;

		foreach ($this->getRflctORM()->getCollections() as $collection) {
			$deleterBuilder = new ORMDeleterBuilder();
			$deleter = $deleterBuilder->build($collection->getTargetEntity(),null,$collection->getName(),$this,$this->getDepth());
				
			$collectionRflctORM = EntityManager::getReflectionData($collection->getTargetEntity());
			$collectionInstance = (is_null($collection->getValue($currentEntity)) ? new Collection(array()) : $collection->getValue($currentEntity));
				
			if ($collection->isOneToMany()) {
				$mappedProperty = $collectionRflctORM->getORMProperty($collection->getMappedBy());

				if ($collection->isDeleteOrphan() ||
				($collection->getCascade() == CascadeType::ALL)||
				($collection->getCascade() == CascadeType::DELETE)) {

					foreach($collectionInstance->toArray() as $current) {
						$deleter->sync($request->getSubRequest($current));
					}
				} else {
					if (!is_null($collectionInstance) && $collectionInstance->size() > 0) {
						throw new Exception("[Runtime|ORM] Um objeto da classe [".$this->getRflctORM()->getName()."] nao pode ser removido pois possui itens na colecao da propriedade OneToMany [{$collection->getName()}] e nao define uma estrategia de CASCADE adequada (ALL,DELETE).");
					}
					/*
					 * Remove automaticamente a referencia ONE dos MANY e deleta a entidade
					 * Comentada mas funcional
					 foreach($collectionInstance->toArray() as $current) {
						$sql = "UPDATE {$driver->formatTable($collectionRflctORM->getTableName())} SET ";

						$collectionIndexes = $collectionRflctORM->getIndexes();
						$entityIndexes = $this->getRflctORM()->getIndexes();

						if ($collection->isComposite()) {
							
						} else {
							
						$collectionIndex = $collectionIndexes[0];
						$entityIndex = $entityIndexes[0];
							
						if (strlen(trim($collectionIndex->getValue($current))) == 0) {
						throw new EntityNotFoundException("Entidade nao persistente incluida na colecao.");
						}
						$sql .= " ".$driver->formatField($mappedProperty->getColumnName())." = NULL";
						}
							
						$sql .= " WHERE ".$driver->formatField($collectionIndex->getColumnName())." = ".$driver->formatValue($collectionIndex->getType(),$collectionIndex->getValue($current));

						$driver->run($sql); //TODO - Testar erro de FK para informar sobre constraint NOT NULL setada para ela.
						}
						*/
				}
			} else if ($collection->isManyToMany()) {

				$joinTable = $collection->getJoinTable();
				$joinColumns = (is_array($collection->getJoinColumns()) ? $collection->getJoinColumns() :  array($collection->getJoinColumns()));
				$inverseJoinColumns = (is_array($collection->getInverseJoinColumns()) ? $collection->getInverseJoinColumns() :  array($collection->getInverseJoinColumns()));
				$thisIndexes = $this->getRflctORM()->getIndexes();
				$collectionIndexes = $collectionRflctORM->getIndexes();

				foreach($collectionInstance->toArray() as $current) {
					$values = array();
					$sql = "DELETE FROM ".$driver->formatTable($joinTable);
						
					//Owner Side
					for ($i = 0; $i < sizeof($joinColumns); $i++) {
						$fields[] = $driver->formatField($joinColumns[$i])." = ".$driver->formatValue($thisIndexes[$i]->getType(),$thisIndexes[$i]->getValue($entity));
					}
						
					//Owned Side
					for ($i = 0; $i < sizeof($inverseJoinColumns); $i++) {
						$fields[] = $driver->formatField($inverseJoinColumns[$i])." = ".$driver->formatValue($collectionIndexes[$i]->getType(),$collectionIndexes[$i]->getValue($current));
					}
						
					$sql = $sql." WHERE ".implode(" AND ",$fields);
					$driver->run($sql);
						
					//Verificar a bidirecionalidade
					if (!$collection->getMappedBy()) {
						if ($collectionRflctORM->isMapping($this->getRflctORM()->getName(),$collection->getName())) {
							$inverseCollectionRflctORM = $collectionRflctORM->getORMProperty($collectionRflctORM->getMapped($this->getRflctORM()->getName(),$collection->getName()));
						} else {
							//throw new ORMException("A colecao da propriedade ".$collection->getName()." da classe ".$this->getRflctORM()->getName()." nao esta mapeada na classe ".$collection->getTargetEntity().". Um dos dois lados do relaciomanento deve conter a opcao @ManyToMany(mappedBy=\"<propriedade>\"...) definida");
							//Unidirecional
							$inverseCollectionRflctORM = false;
						}
					} else {
						$inverseCollectionRflctORM = $collectionRflctORM->getORMProperty($collection->getMappedBy());
					}
						
					if ($inverseCollectionRflctORM) {
						$inverseCollectionInstance = $inverseCollectionRflctORM->getValue($current);
						if (!is_null($inverseCollectionInstance)) {
							if ($inverseCollectionInstance->contains($entity)) {
								$inverseCollectionInstance->remove($entity);
								$inverseCollectionInstance->resetState($entity);
							} else {
								throw new ORMException("[Runtime] Erro de consistencia. Uma objeto contido na colecao [".$this->getRflctORM()->getName().".".$collection->getName()."] existe somente em um dos lados do relacionamento");
							}
						} else {
							if (is_null($inverseCollectionInstance)) throw new ORMException("[Runtime] Erro de consistencia. O relacionamento bidirecional [".$this->getRflctORM()->getName().".".$collection->getName()."] possui uma instancia de colecao somente em um dos lados do relacionamento");
						}
					}
						
					if (($collection->getCascade() == CascadeType::ALL)||
					($collection->getCascade() == CascadeType::DELETE)) {

						//Verifica cache
						If (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($current))) {
							$deleter->sync($request->getSubRequest($current));
						}
					}
				}
				$collectionInstance->clear();
				$collectionInstance->resetState();
			}
		}
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
			if (!$current->getRflctORM()->getParentClass() && (is_null($currentEntity) || $request->hasProcessed($currentEntity,$current->getRflctORM()->getName()))) {
				$current = $current->getNext();
				continue;
			}
			EntityManager::getCacher()->remove(EntityManager::getCacher()->getOIDString($currentEntity));
			$current->deleteCollections($request);
			$sql = $current->buildSQL($request);
			$driver->run($sql);
			
			//Remover referencias - TODO criar metodo proprio [beta] 
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
								$joinedEntityCollection->remove($currentEntity);
								$joinedEntityCollection->resetState($currentEntity);
							}
						} 
					}
					//$join->setValue($currentEntity,null);
				}
			}
			
			//Remover indices
			foreach ($current->getRflctORM()->getIndexes() as $index) {
				if ($index->isForeignKey()) {
					continue;
				} else {
					$index->setValue($currentEntity,null);
				}
			}
			$request->setProcessed($currentEntity,$current->getRflctORM()->getName());
			$current = $current->getNext();
		}
	}
}

?>
