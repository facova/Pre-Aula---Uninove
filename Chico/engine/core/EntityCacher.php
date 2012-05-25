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
 * @subpackage core
 * File: EntityCacher.php
 **/

import('engine.db.CachedObjectContainer');

/** Gerencia instancias de entidades.
 * @author Silas R. N. Junior
 */
class EntityCacher {

	/** cache de objetos
	 * @var array
	 */
	private $data = array();
	
	/** Referencias das entidades
	 * @var array
	 */
	private $references = array();

	/** Adiciona uma instancia ao cache
	 * @param string $oid   Identificador do objeto
	 * @param object $obj   Objeto a ser colocado em cache
	 * @param int $version   Versao do Objeto no Banco
	 */
	public function cache($oid, &$obj, $version = null) {
		if (!is_string($oid)) throw new Exception("[Cacher]O parametro passado nao e um identificador valido.");
		if (!is_object($obj)) throw new Exception("[Cacher]O parametro passado nao corresponde a um objeto.");
		$this->remove($oid);
		$this->data[$oid] = new CachedObjectContainer($obj,$version);
	}

	/** Remove uma instancia do cache
	 * @param string $oid   Identificador do objeto
	 */
	public function remove($oid) {
		if (!is_string($oid)) throw new Exception("O parametro passado nao e um identificador valido.");
		if (isset($this->data[$oid])) {
			$this->data[$oid] = null;
			unset($this->data[$oid]);
		}
	}

	/** Recupera uma instancia pelo identificador
	 * @param string $oid   Identificador do objeto
	 */
	public function &lookup($oid) {
		$false = false; //Evitar warning
		if (isset($this->data[$oid])) {
			$value = &$this->data[$oid]->getSubject();
			return $value;
		} else {
			return $false;
		}
	}
	
	public function getVersion($oid) {
		if (isset($this->data[$oid])) {
			$value = $this->data[$oid]->getVersion();
			return $value;
		} else {
			throw new Exception("Item nao encontrado em cache (".$oid.").");
		}
	}
	
	public function setVersion($oid,$version) {
		
		if (isset($this->data[$oid])) {
			$value = $this->data[$oid]->setVersion($version);
			return $value;
		} else {
			throw new Exception("Item nao encontrado em cache (".$oid.").");
		}
	}

	/** Limpa o cache
	 * @return void
	 */
	public function clear() {
		$this->data = null;
		$this->data = array();
	}
	
	private function storeReferences($entity) {
		$classMeta = EntityManager::getReflectionData(get_class($entity));
		$oid = $this->getOIDString($entity);
		foreach ($classMeta->getORMProperties() as $property) {
			if (in_array($property->getType(),get_declared_classes())) {
				$obj = $property->getValue($entity);
				if (isset($obj) && !is_a($obj,"Collection")) {
					$objOID = $this->getOIDString($obj);
					if (isset($objOID)) {
						$this->references[$objOID] = array();
						$this->references[$objOID][$property->getName()] = $oid;
					}
				}
			}
		}
	}
	
	private function updateReferences($entity) {
		$oid = $this->getOIDString($entity);
		if (isset($this->references[$oid])) {
			foreach ($this->references[$oid] as $propertyName => $objOID) {
				$obj = $this->lookup($objOID);
				$rf = EntityManager::getReflectionData($obj);
				$rf->getORMProperty($propertyName)->setValue($obj, $entity);
			}
		}
	} 

	public function getOIDString($entity) {
		$classMeta = EntityManager::getReflectionData(get_class($entity));
		$oidName = $classMeta->getName();
		if($classMeta->getParentClass()) {
			$classMeta = $classMeta->getParentORMClass();
		}
		foreach ($classMeta->getIndexes() as $column) {
			if ($column->getValue($entity) == null) return false;
			if ($column->isForeignKey()) {
				foreach ($column->getForeignIndexORMProperties() as $currentIndex) {
					$oidName .= "#".$currentIndex->getValue($entity);
				}
			} else {
				$oidName .= "#".$column->getValue($entity);
			}
		}
		return $oidName;
	}

	/** Retorna os dados do cache de um objeto
	 * @param string $oid    Identificador do objeto
	 * @return CachedObjectContainer
	 */
	public function getCacheData($oid) {
		if (isset($this->data[$oid])) {
			return $this->data[$oid];
		} else {
			throw new Exception("Item nao encontrado em cache (".$oid.").");
		}
	}
	
	
	function __destruct() {
		if (defined('ENGINE_SESSION_CACHE')) {
			$_SESSION['ENGINE_CACHE'] = serialize($this);
		}
	}
	
	/** Fazer o cache sobreviver na sessao
	 */
	function __sleep() {
		foreach ($this->data as $oid => $object) {
			$this->data[$oid] = new SerializableContainer($object);
			$this->storeReferences($object->getSubject());
		}
		return array('data','references');
	}
	
	function updateReferenceData() {
		foreach ($this->data as $oid => $serializedContainer) {
			$this->data[$oid] = $serializedContainer->getSubject();
		}
		//Corrigir referencias
		foreach ($this->data as $oid => $entity) {
			$this->updateReferences($entity->getSubject());
			
			//Colecoes
			$entity = $entity->getSubject();
			$rf = EntityManager::getReflectionData($entity);
			foreach ($rf->getCollections() as $collection) {
				$col = $collection->getValue($entity);
				if ($col) {
					$col->updateReferences();
				}
			}
			/*
			$entity = $entity->getSubject();
			$rf = EntityManager::getReflectionData($entity);
			foreach ($rf->getORMProperties() as $property) {
				if (in_array($property->getType(),get_declared_classes())) {
					$obj = $property->getValue($entity);
					if (isset($obj)) {
						$cachedObj = $this->lookup($this->getOIDString($obj));
						if (isset($cachedObj)) {
							$property->setValue($entity,$cachedObj);
						}
					}
				}
			}
			*/
		}
	}
}

?>
