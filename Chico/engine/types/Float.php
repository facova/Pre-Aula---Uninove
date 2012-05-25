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
 * File: Float.php
 **/

import('engine.types.Type');

/** Fornce funcoes para operacoes com numeros de ponto flutuante
 * @author Silas R. N. Junior
 */
class Float extends Type {

	/**
	 * @var int
	 */
	private $value;

	/**
	 */
	public function Float($value) {
		$this->value = $value;
	}

	/** Retorna um numero de ponto flutuante baseado na string fornecida
	 * @param string $s
	 * @return float
	 */
	public function parseFloat($s) {
		return (double)$s;
	}

	/** Retorna o numero de ponto flutuante contido no objeto
	 * @return float
	 */
	public function floatValue() {
		return (double)$this->value;
	}

	/** Retorna o valor corrente do tipo em string
	 * @return string
	 */
	public function __toString() {
		return (string)$this->value;
	}
}

?>