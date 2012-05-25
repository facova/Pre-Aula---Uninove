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
 * File: Money.php
 **/

import("engine.types.Type");

/** Fornece funcoes para operacao com moedas
 * @author Silas R. N. Junior
 */
class Money extends Type {

	/**
	 * @var float
	 */
	private $value;

	/**
	 */
	public function Money($value) {
		$this->value = $value;
	}

	/**
	 * @return int
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param int $newValue
	 * @return void
	 */
	public function setValue($newValue) {
		$this->value = $newValue;
	}

	/** Retorna o valor ajustado com a taxa fornecida
	 * @param float $tax
	 * @return float
	 */
	public function getAdjustedBy($tax) {
		/** <TODO> Implement. */
	}

	/** Retorna o valor corrente do tipo em string
	 * @return string
	 */
	public function __toString() {
		return (string)$this->value;
	}
}

?>