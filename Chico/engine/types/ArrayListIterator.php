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
 * File: ArrayListIterator.php
 **/


/** Iterador para lista.
 * @author Silas R. N. Junior
 */
class ArrayListIterator {

	/** Ponteiro interno
	 * @var int
	 */
	private $ptr = -1;

	/** ArrayList do Iterador
	 * @var ArrayList
	 */
	private $arrayList;

	/**
	 * @param ArrayList $arrayList
	 */
	public function ArrayListIterator(ArrayList $arrayList) {
		$this->arrayList = $arrayList;
	}

	/** Verifica se existe um proximo elemento na lista
	 * @return boolean
	 */
	public function hasNext() {
		if ($this->arrayList->hasIndex($this->ptr + 1))
		return true;
		else
		return false;
	}

	/** Verifica se existe um elemento anterior na lista
	 * @return boolean
	 */
	public function hasPrevious() {
		if ($this->arrayList->hasIndex($this->ptr - 1))
		return true;
		else
		return false;
	}

	/** Retorna o proximo elemento da lista incrementando o indice.
	 * @return mixed
	 */
	public function next() {
		$this->ptr++;
		return $this->arrayList->get($this->ptr);
	}

	/** Retorna o indice do proximo elemento
	 * @return int
	 */
	public function nextIndex() {
		if ($this->hasNext()) {
			return $this->ptr + 1;
		}
	}

	/** Retorna o indice do elemento anterior
	 * @return int
	 */
	public function previousIndex() {
		if ($this->hasPrevious()) {
			return $this->ptr - 1;
		}
	}

	/** Adiciona um elemento na lista imediatamente antes do elemento que sera chamado pelo metodo next.
	 * @param mixed $e   Elemento a ser adicionado
	 * @return void
	 */
	public function add($e) {
		$arr = $this->arrayList->toArray();
		$tmp = array_splice($arr,$this->ptr);
		$arr[] = $e;
		$arr = array_merge($arr,$tmp);
		$this->arrayList->clear();
		$this->arrayList->addArray($arr);
	}

	/** Remove o elemento da ultima chamada a next() ou previous(). Nao podera ser chamada mais de uma vez por chamada a next() ou previous(). Nao podera ser utilizada se add() foi utilizado apos a ultima chamada next() ou previous()
	 * @return void
	 */
	public function remove() {
		$arr = $this->arrayList->toArray();
		$tmp = array_splice($arr,$this->ptr,1);
		$this->arrayList->clear();
		$this->arrayList->addArray($arr);
	}
}

?>