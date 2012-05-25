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
 * @subpackage types
 * File: Collection.php
 **/

import("engine.types.ArrayList");

/** Colecao de objetos
 * @author Silas R. N. Junior
 */
class Collection extends ArrayList {

	/**
	 * @var array
	 */
	private $added = array();

	/**
	 * @var array
	 */
	private $removed = array();

	/**
	 * @var array
	 */
	//private $initial = array();

	/**
	 * @param array $array   Array de inicializacao do objeto [opcional]
	 */
	public function Collection($array = null) {
		if (isset($array)) {
			$this->init($array);
		}
		parent::ArrayList($array);
	}

	private function init ($array) {
		foreach ($array as $element) {
			if (!is_object($element)) {
				throw new Exception ("A colecao pode conter somente objetos");
			}
			//$this->initial[spl_object_hash($element)] = array('obj' => $element, 'state' => md5(serialize($element)));
		}
	}

	/** Adiciona um objeto a colecao
	 * @param object $object   Objeto a ser adicionado
	 */
	public function add($object) {
		if ($object instanceof IEntityContainer) {
			$hash =	spl_object_hash($object->getSubject());
		} else {
			$hash =	spl_object_hash($object);
		}

		if (parent::contains($object)) {
			throw new Exception("A Colecao contem o objeto fornecido");
		} else {
			if (isset($this->removed[$hash])) {
				$this->removed[$hash] = null;
				unset($this->removed[$hash]);
			} else {
				$this->added[$hash] = $object;
			}
			return parent::add($object);
		}
	}

	/** Remove um objeto
	 * @param object $object   Objeto a ser removido
	 */
	public function remove($object) {
			
		if ($object instanceof IEntityContainer) {
			$hash =	spl_object_hash($object->getSubject());
		} else {
			$hash =	spl_object_hash($object);
		}
			
		if (parent::contains($object)) {
			if (isset($this->added[$hash])) {
				$this->added[$hash] = null;
				unset($this->added[$hash]);
			} else {
				$this->removed[$hash] = $object;
			}
			parent::remove($object);
		} else {
			throw new Exception("A Colecao nao contem o objeto fornecido");
		}
	}

	/** Retorna o elemento na posicao especificada
	 * @param mixed $idx   Indice do Elemento [numerico ou textual]
	 * @return mixed
	 */
	public function get($idx) {
		if (parent::get($idx) instanceof IEntityContainer) {
			parent::get($idx)->init();
		}
		return parent::get($idx);
	}

	/** Retorna um array com os objetos adicionados
	 * @return array
	 */
	public function getAdded() {
		return $this->added;
	}

	/** Retorna um array com os objetos removidos
	 * @return array
	 */
	public function getRemoved() {
		return $this->removed;
	}

	/** Retorna um array com os objetos modificados apos terem sido adicionados
	 * Este metodo funciona com uma chamada ao objeto de cacher. 
	 * @return array
	 * @deprecated
	 */
	public function getModified() {
		$arr = parent::toArray();
		$modified = array();
		foreach ($arr as $element) {
			if (EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($element))) {
				if (EntityManager::getCacher()->getCacheData(EntityManager::getCacher()->getOIDString($element))->isDirty()) $modified[] = $element;
			}
			/* Codigo antigo, useless. Problemas com alteracoes nos estados das colecoes inviabiliza manter as strings
			 * internamente. Uma vez serializado o objeto ao ter a string alterada na colecao ficava automaticamente obsoleto
			if (isset($this->initial[spl_object_hash($element)])) {
				if (md5(serialize($element)) != $this->initial[spl_object_hash($element)]['state']) {
					$modified[] = $element;
				}
			}
			*/
		}
		return $modified;
	}

	/** Redefine o status da colecao
	 * @return void
	 */
	public function resetState($obj = null) {
		if ($obj) {
			if ($obj instanceof IEntityContainer) {
				$hash =	spl_object_hash($obj->getSubject());
			} else {
				$hash =	spl_object_hash($obj);
			}
				
			if (!is_object($obj)) {
				throw new Exception ("A colecao pode conter somente objetos");
			}
				
			if (isset($this->removed[$hash]) && !is_null($this->removed[$hash])) {
				//$this->initial[$hash] = null;
				//unset($this->initial[$hash]);
				$this->removed[$hash] = null;
				unset($this->removed[$hash]);
			}
			if (isset($this->added[$hash]) && !is_null($this->added[$hash])) {
				//$this->initial[$hash] = array('obj' => $obj, 'state' => md5(serialize($obj)));
				$this->added[$hash] = null;
				unset($this->added[$hash]);
			}
			/*
			if (isset($this->initial[$hash]) && !isset($this->removed[$hash]) && !isset($this->added[$hash])) {
				$this->initial[$hash] = array('obj' => $obj, 'state' => md5(serialize($obj)));
			}
			*/
				
		} else {
			//$this->initial = array();
			$this->removed = array();
			$this->added = array();
			$array = parent::toArray();
			parent::clear();
			$this->init($array);
			parent::ArrayList($array);
		}
	}

	/**
	 * Limpa a colecao
	 */
	public function clear() {
		foreach (parent::toArray() as $element) {
			$this->remove($element);
		}
	}

	public function updateReferences() {
		
		for ($i = 0; $i < sizeof($this->a); $i++) {
			$cachedElement = EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($this->a[$i]));
			if ($cachedElement) {
				if ($cachedElement instanceof IEntityContainer) {
					$cachedHash = spl_object_hash($cachedElement->getSubject());
				} else {
					$cachedHash = spl_object_hash($cachedElement);
				}
				if ($cachedHash != spl_object_hash($this->a[$i])) {
					$this->a[$i] = &$cachedElement;
				}
			}
		}
		
		foreach ($this->added as $hash => $element) {
			$cachedElement = EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($element));
			if ($cachedElement) {
				if ($cachedElement instanceof IEntityContainer) {
					$cachedHash = spl_object_hash($cachedElement->getSubject());
				} else {
					$cachedHash = spl_object_hash($cachedElement);
				}
				if ($cachedHash != $hash) {
					$this->added[$cachedHash] = $cachedElement;
					unset($this->added[$hash]);
				}
			}
		}
		
		foreach ($this->removed as $hash => $element) {
			$cachedElement = EntityManager::getCacher()->lookup(EntityManager::getCacher()->getOIDString($element));
			if ($cachedElement) {
				if ($cachedElement instanceof IEntityContainer) {
					$cachedHash = spl_object_hash($cachedElement->getSubject());
				} else {
					$cachedHash = spl_object_hash($cachedElement);
				}
				if ($cachedHash != spl_object_hash($element)) {
					$this->removed[$cachedHash] = $cachedElement;
					unset($this->removed[$hash]);
				}
			}
		}
		/*
		$arr = array();
		foreach ($this->initial as $element) {
			if ($element['obj'] instanceof IEntityContainer) {
				$hash =	spl_object_hash($element['obj']->getSubject());
			} else {
				$hash =	spl_object_hash($element['obj']);
			}
			$arr[$hash] = $element;
		}
		$this->initial = $arr;
		*/
	}
}

?>
