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
 * File: ArrayList.php
 **/


/** Conteiner para array. Fornece metodos para manipulacao de seus elementos
 * @author Silas R. N. Junior
 */
class ArrayList extends Object {

	/**
	 * @var array
	 */
	protected $a = array();

	/**
	 * @param array $array   Array de inicializacao do objeto [opcional]
	 */
	public function ArrayList($array = null) {
		$this->a = (isset($array)&&is_array($array)) ? $array : array();
	}

	/** Adiciona um elemento ao final da lista.
	 * @param mixed $e
	 * @return int Indice inserido
	 */
	public function add($e) {
		$this->a[] = $e;
		end($this->a);
		$idx = key($this->a);
		return $idx;
	}

	/** Retira o elemento referenciado pelo indice fornecido da lista
	 * @param mixed $idx   Indice do elemento a remover
	 * @return void
	 */
	public function remove($idx) {
		if (is_object($idx)) {
			for ($i = 0; $i < count($this->a); $i++) {
				if (is_object($this->a[$i])) {
					if (spl_object_hash($this->a[$i]) == spl_object_hash($idx)) {
						$tmp = $this->a[$i];
						array_splice($this->a,$i,1);
						return $tmp;
					}
				}
			}
			throw new Exception("Elemento Inexistente [Indice: Objeto]");
		} else if($this->a[$idx]) {
			$tmp = $this->a[$idx];
			array_splice($this->a,$idx,1);
			return $tmp;
		} else {
			throw new Exception("Elemento Inexistente [Indice:$idx]");
		}
	}

	/** Retorna um Iterator para a Lista.
	 * @param int $i   [Opcional: posiciona o indice no argumento fornecido.]
	 * @return ArrayListIterator
	 */
	public function iterator($i = null) {
		return new ArrayListIterator($this);
	}

	/** Retorna o elemento na posicao especificada
	 * @param mixed $idx   Indice do Elemento [numerico ou textual]
	 * @return mixed
	 */
	public function get($idx) {
		if(isset($this->a[$idx])) {
			return $this->a[$idx];
		} else {
			throw new Exception("Elemento Inexistente [Indice:$idx]");
		}
	}

	/** Substitui o elemento no indice especificado pelo fornecido
	 * @param mixed $idx
	 * @param mixed $element
	 * @return mixed
	 */
	public function set($idx, $element) {
		if($this->a[$idx]) {
			$tmp = $this->a[$idx];
			$this->a[$idx] = $element;
			return $tmp;
		} else {
			throw new Exception("Elemento Inexistente [Indice:$idx]");
		}
	}

	/** Verifica a existencia de um indice
	 * @param mixed $value
	 * @return boolean
	 */
	public function hasIndex($value) {
		if (isset($this->a[$value]))
		return true;
		else
		return false;
	}

	/** Verifica se o elemento existe na lista
	 * @param mixed $element
	 * @return boolean
	 */
	public function contains($element) {
		foreach ($this->a as $e) {
			if (is_object($e) && is_object($element)) {
				if (spl_object_hash($e) == spl_object_hash($element)) {
					return true;
				}
			} else {
				if ($e === $element) {
					return true;
				}
			}
		}
		return false;
	}

	/** Remove todos os elementos da lista
	 * @return void
	 */
	public function clear() {
		$this->a = array();
	}

	/** Adiciona os elementos do Array a Lista
	 * @param array $a   Array de elementos
	 * @return boolean
	 */
	public function addArray($a) {
		$this->a = array_merge($this->a,$a);
	}

	/** Retorna os dados em um array
	 * @return array
	 */
	public function toArray() {
		return $this->a;
	}

	/** Retorna a quantidade de elementos na lista
	 * @return int
	 */
	public function size() {
		return count($this->a);
	}
}

?>