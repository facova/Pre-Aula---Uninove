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
 * File: ORMProxy.php
 **/


/** Pattern Proxy para fins genéricos
 * @author Silas R. N. Junior
 */
class ORMProxy {

	/** Objeto encapsulado
	 * @var object
	 */
	private $object;

	/** Classe de reflexao do objeto
	 * @var ReflectionORMClass
	 */
	private $class;

	/** Flag de inicializacao de objetos compostos
	 * @var boolean
	 */
	private $initialize;

	/**
	 * @param mixed $entity   Objeto ou Classe do proxy
	 * @param boolean $initialize   Flag de inicializacao de objetos compostos [default: false]
	 */
	public function ORMProxy(&$entity, $initialize = false) {
		$this->initialize = $initialize;
		if (is_object($entity)) {
			if ($entity instanceof ReflectionClass ) {
				$this->object = $entity->newInstance();
				$this->class = EntityManager::getReflectionData($entity->getName());
			} else {
				$this->object = $entity;
				$this->class = EntityManager::getReflectionData(get_class($entity));
			}
		} else if (is_string($entity)) {
			if (class_exists($entity)) {
				$this->object = new $entity();
				$this->class = EntityManager::getReflectionData($entity);
			} else {
				throw new Exception("Erro criando proxy para classe ".$entity.". Classe Inexistente ou nao incluida.");
			}
		} else if (!isset($entity)) {
			throw new Exception("Erro criando proxy. Classe não informada.");
		}
	}

	/**
	 */
	public function &__get($property) {
		$null = null;
		$path = explode(".",$property);
		if (count($path) > 1) {
			$prop = array_splice($path,0,1);
			
			if ($this->class->hasProperty($prop[0])) {
				$ORMproperty = $this->class->getORMProperty($prop[0]);
				if ($ORMproperty->getValue($this->object) != null) {
					$v = $ORMproperty->getValue($this->object);
					$object = new ORMProxy($v,$this->initialize);
				} else if ($this->initialize) {
					//$var = $ORMproperty->getType();
					$object = new ORMProxy($this->_getInitializedObject_($ORMproperty),$this->initialize);
					//$this->object->{EntityUtils::getSetter($prop[0])}($object->getSubject());
					$ORMproperty->setValue($this->object,$object->getSubject());
				}
				if (isset($object)) {
					$p = implode(".",$path);
					$ret = $object->{$p};
					return $ret;
				} else {
					return $null;
				}
			}
		} else {
			$rf = $this->class;
			/*
			 * [HACK] hasProperty nao funciona da forma desejada.
			 */
			$props = $rf->getORMProperties();
			$has = false;
			foreach ($props as $p) {
				if ($p->name == $property) {
					$has = true;
					break;
				}
			}
			if (!$has) {
				while ($rf->getParentClass()) {
					$rf = $rf->getParentORMClass();
					$props = $rf->getProperties();
					foreach ($props as $p) {
						if ($p->name == $property) {
							$has = true;
							break;
						}
					}
				}
				if (!$has) {
					throw new Exception("A classe ".$this->class." nao possui a propriedade ".$property);
				}
			}
			/*
			 * [HACK] Fim do hack
			 */
			$rp = $rf->getORMProperty($property);


			$value = $rp->getValue($this->object);

			if (is_null($value)) {
					
				if ($rp->hasAnnotation('var')) {
					$var = trim($rp->getAnnotation('var'));
				} else {
					$var = "string";
				}
				if (!class_exists($var) || in_array($var,Annotations::$ignore) || in_array($var,array("string","int","integer","boolean","float","date","time","money"))) {
					return $value;
				} else {
					if ($this->initialize) {
						//return new ORMProxy($this->_getInitializedObject_($property));
						$rp = $this->_getInitializedObject_($rp);
						return $rp;
					} else {
						return $value;
					}
				}
			} else {
				return $value;
			}
		}
	}

	/**
	 */
	public function __set($property,$value) {
		$path = explode(".",$property);
		if (count($path) > 1) {
			$prop = array_splice($path,count($path) - 1,1);
			$object = new ORMProxy($this->{implode(".",$path)});
			$object->{$prop[0]} = $value;
		} else {
			if (is_callable(array($this->object,EntityUtils::getSetter($property)))) {
				$this->object->{EntityUtils::getSetter($property)}($value);
			} else {
				throw new ReflectionException("A classe ".get_class($this->object)." nao possui o metodo Setter chamado [".$property."]");
			}
		}
	}

	/**
	 */
	public function __call($name, $arguments) {
		if (is_callable(array($this->object,$name))) {
			if ($arguments) {
				return call_user_func_array(array($this->object,$name),$arguments);
			} else {
				return call_user_func(array($this->object,$name));
			}
		}
	}

	/**
	 */
	public function __destruct() {
		$this->class = null;
		$this->object = null;
	}

	private function _getInitializedObject_(ReflectionORMProperty $property) {
		
		if ($property->getType() == "Collection") {
			$rf = EntityManager::getReflectionData($property->getTargetEntity());
		} else {
			$rf = EntityManager::getReflectionData($property->getType());
		}
		
		$ob = $rf->newInstance();

		if ($property->isCollection()) {
			return;
		}
		$bDir = false;
		//Verifica se o mapeamento e bidirecional
		if ($property->getMappedBy()) {
			$bDir =  $property->getMappedBy();
		} else if ($rf->isMapping($this->class->getName(),$property->getName())) {
			$bDir =  $rf->getMapped($this->class->getName(),$property->getName());
		}

		if ($bDir) {
			$mappedProperty = $rf->getORMProperty($bDir);
			if ($mappedProperty->getType() != "Collection") {
				$mappedProperty->setValue($ob,$this->object);
			} else {
				//Nao tratado ainda
			}
		}
		$property->setValue($this->object,$ob);
		return $ob;
	}

	public function getSubject() {
		return $this->object;
	}
}
?>