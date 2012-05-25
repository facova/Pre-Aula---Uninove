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
 * @subpackage mvc
 * File: Request.php
 **/


/** Contem os dados de uma requisicao
 * @author Silas R. N. Junior
 */
class Request {

	/** Recupera uma variavel da requisicao
	 * @param string $name   Nome da variavel
	 * @return string
	 */
	public static function getVar($name) {
		if (isset($_REQUEST[$name]))
		return $_REQUEST[$name];
		else
		return false;
	}

	/** Define uma variavel na requisicao
	 * @param string $name   Nome da variavel
	 * @param mixed $value   Valor da variavel
	 * @return void
	 */
	public static function setVar($name, $value) {
		$_REQUEST[$name] = $value;
	}

	/** Verifica se uma variavo com o nome informado existe na requisicao
	 * @param string $name   Nome da variavel
	 * @return boolean
	 */
	public static function hasVar($name) {
		return isset($_REQUEST[$name]);
	}
}

?>