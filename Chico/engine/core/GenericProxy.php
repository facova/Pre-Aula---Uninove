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
 * File: GenericProxy.php
 **/


/** Pattern Proxy para fins genéricos
 * @author Silas R. N. Junior
 */
class GenericProxy {

	/** Objeto encapsulado
	 * @var object
	 */
	private $object;

	/** Nome da classe do objeto
	 * @var string
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
	public function GenericProxy($entity, $initialize = false) {
		$this->initialize = $initialize;
		if (is_object($entity)) {
			if ($entity instanceof ReflectionClass ) {
				$this->object = $entity->newInstance();
				$this->class = $entity->getName();
			} else {
				$this->object = $entity;
				$this->class = get_class($entity);
			}
		} else if (is_string($entity)) {
			if (class_exists($entity)) {
				$this->object = new $entity();
				$this->class = $entity;
			} else {
				throw new Exception("Erro criando proxy para classe ".$entity.". Classe Inexistente ou nao incluida.");
			}
		} else if (!isset($entity)) {
			throw new Exception("Erro criando proxy. Classe não informada.");
		}
	}

	/**
	 */
	public function __get($property) {
		$path = explode(".",$property);
		if (count($path) > 1) {
			$prop = array_splice($path,0,1);
			if (is_callable(array($this->object,EntityUtils::getGetter($prop[0])))) {
				if ($this->object->{EntityUtils::getGetter($prop[0])}() != null) {
					$object = new GenericProxy($this->object->{EntityUtils::getGetter($prop[0])}(),$this->initialize);
				} else if ($this->initialize) {
					$rf = EntityManager::getClassMeta($this->class)->getReflectionClass();
					$rp = $rf->getPropertyAnnotated($prop[0]);
					$var = $rp->getAnnotation('var');
					$object = new GenericProxy($this->_getInitializedObject_($prop[0]),$this->initialize);
					$this->object->{EntityUtils::getSetter($prop[0])}($object->getSubject());
				}
				if (isset($object)) {
					return $object->{implode(".",$path)};
				} else {
					return;
				}
			}
		} else {
			$prop = $property;
				
			$rf = EntityManager::getClassMeta($this->class)->getReflectionClass();
			/*
			 * [HACK] hasProperty nao funciona da forma desejada.
			 */
			$props = $rf->getProperties();
			$has = false;
			foreach ($props as $p) {
				if ($p->name == $prop) {
					$has = true;
					break;
				}
			}
			if (!$has) {
				while ($rf->getParentClass()) {
					$rf = $rf->getParentClassAnnotated();
					$props = $rf->getProperties();
					foreach ($props as $p) {
						if ($p->name == $prop) {
							$has = true;
							break;
						}
					}
				}
				if (!$has) {
					throw new Exception("A classe ".$this->class." nao possui a propriedade ".$prop);
				}
			}
			/*
			 * [HACK] Fim do hack
			 */
			$rp = $rf->getPropertyAnnotated($prop);

			if (is_callable(array($this->object,EntityUtils::getGetter($rp)))) {

				$value = $this->object->{EntityUtils::getGetter($rp)}();

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
							//return new GenericProxy($this->_getInitializedObject_($property));
							return $this->_getInitializedObject_($rf->getName(),$property);
						} else {
							return $value;
						}
					}
				} else {
					return $value;
				}
			} else {
				throw new ReflectionException("A classe ".get_class($this->object)." nao possui o metodo Getter chamado [".$prop."]");
			}
		}
	}

	/**
	 */
	public function __set($property,$value) {
		$path = explode(".",$property);
		if (count($path) > 1) {
			$prop = array_splice($path,count($path) - 1,1);
			$object = new GenericProxy($this->{implode(".",$path)});
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

	private function _getInitializedObject_($class,$property) {
		$rf = EntityManager::getClassMeta($class)->getReflectionClass();
		$rp = $rf->getPropertyAnnotated($property);
		 
		$propClass = trim($rp->getAnnotation('var'));
		//**********************************************************************************************************************
		//trabalhar com interfaces
		if (interface_exists($propClass)) {
			$tempManyToOne = $rp->getAnnotation("ManyToOne");
			$tempOneToOne = $rp->getAnnotation("OneToOne");
			if (isset($tempManyToOne))
			$class = trim($rp->getAnnotation("ManyToOne")->getTargetEntity());
			elseif (isset($tempOneToOne))
			$class = trim($rp->getAnnotation("OneToOne")->getTargetEntity());
			else
			throw new LogicException("A propriedade ".$rp->getName().
    						" foi tipada como uma interface e deve possuir a anotacao targetEntity='<nome_classe>' setada.");
		}
		//**********************************************************************************************************************
		$ob = new $propClass();

		$property = EntityManager::getClassMeta($class)->getPropertyMeta($property);
		 
		if ($property->getAssociationMeta() instanceof OneToManyAnnotation) {
			return;
		} else if ($property->getAssociationMeta() instanceof ManyToManyAnnotation) {
			return;
		}

		//Verifica se o mapeamento e bidirecional
		if ($property->getAssociationMeta()->getMappedBy()) {
			$bDir =  $property->getAssociationMeta()->getMappedBy();
		} else if (EntityManager::getClassMeta($property->getType(true))->isMapping($class,$property->getReflectionProperty()->getName())) {
			$bDir =  EntityManager::getClassMeta($property->getType(true))->getMapped($class,$property->getReflectionProperty()->getName());
		}

		if ($bDir) {
			if (EntityManager::getClassMeta($propClass)->getPropertyMeta($bDir)->getType() != "Collection") {
				$ob->{EntityUtils::getSetter($bDir)}($this->object);
			} else {

			}
		}
		$this->object->{EntityUtils::getSetter($property->getReflectionProperty()->getName())}($ob);
		return $ob;
	}

	public function getSubject() {
		return $this->object;
	}
}
?>