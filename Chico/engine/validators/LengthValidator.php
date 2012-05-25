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
 * @subpackage validators
 * File: LengthValidator.php
 **/


/** Verifica se o tamanho da string confere
 * @author Silas R. N. Junior
 */
class LengthValidator extends Validator {

	/**
	 * @var int
	 */
	private $min = 0;

	/**
	 * @var int
	 */
	private $max;

	/**
	 * @return int
	 */
	public function getMin() {
		return $this->min;
	}

	/**
	 * @param int $newMin
	 * @return void
	 */
	public function setMin($newMin) {
		$this->min = $newMin;
	}

	/**
	 * @return int
	 */
	public function getMax() {
		return $this->max;
	}

	/**
	 * @param int $newMax
	 * @return void
	 */
	public function setMax($newMax) {
		$this->max = $newMax;
	}

	/** Verifica se o valor é valido
	 * @param mixed $value   Valor
	 * @return boolean
	 */
	public function isValid($value) {
		if ($this->getMax()) {
			return ((strlen($value) >= $this->getMin())&&(strlen($value) <= $this->getMax())) ? true : false;
		} else {
			return (strlen($value) >= $this->getMin()) ? true : false;
		}
	}

	/** Retorna a mensagem de erro
	 * @return string
	 */
	public function message() {
		$msg1 = "";
		$msg2 = "";
		if ($this->getMin() > 0) {
			$msg1 = " maior que ".$this->getMin();
		}
		if ($this->getMax() > 0) {
			$msg2 = " menor que ".$this->getMax();
		}
		return "O campo deve possuir uma quantidade de caracteres".$msg1.$msg2;
	}
}

?>