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
 * File: NotNullValidator.php
 **/


/** Verifica se o valor nao e nulo
 * @author Silas R. N. Junior
 */
class NotNullValidator extends Validator {

	/** Verifica se o valor é valido
	 * @param mixed $value   Valor
	 * @return boolean
	 */
	public function isValid($value) {
		return !is_null($value) ? true : false;
	}

	/** Retorna a mensagem de erro
	 * @return string
	 */
	public function message() {
		return "O valor nao pode estar vazio";
	}

}

?>