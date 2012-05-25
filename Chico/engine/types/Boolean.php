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
 * File: Boolean.php
 **/

import("engine.types.Type");

/** Fornce funcoes para operacoes com Booleanos
 * @author Silas R. N. Junior
 */
class Boolean extends Type {

	/**
	 * @var boolean
	 */
	private $value;

	/**
	 */
	public function Boolean($value) {
		$this->value = $value;
	}

	/** Retorna o valor booleano baseado no argumento fornecido
	 * @param mixed $m
	 * @return boolean
	 */
	public function parseBoolean($m) {
		/** <TODO> Implement. */
	}

	/** Retorna o valor booleano do objeto
	 * @return boolean
	 */
	public function getBoolean() {
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