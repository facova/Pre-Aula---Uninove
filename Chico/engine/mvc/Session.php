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
 * File: Session.php
 **/


/** Contem os dados armazenados na sessao do usuario
 * @author Silas R. N. Junior
 */
class Session {

	private static $initialized = false;
	/**
	 */
	public static function init() {
		@session_start();
		self::$initialized = true;
	}

	/** destroi a sessao
	 * @return void
	 */
	public static function destroy() {
		session_destroy();
		session_unset();
	}

	/** cria ou atualiza uma variavel na sessao
	 * @param string $name   Nome da variavel
	 * @param mixed $value   Valor da variavel
	 * @return void
	 */
	public static function set($name, $value) {
		if (!self::$initialized) {
			trigger_error("Utilizacao de sessao nao inicializada (ou inicializada via session_start diretamente), forcando inicializacao...\nConsidere utilizar Session::init() no inicio de seu script.", E_USER_NOTICE);
			self::init();
		}
		if(is_object($value)) {
			$serializable = new SerializableContainer($value);
			$_SESSION[$name] = $serializable;
		} else {
			$_SESSION[$name] = $value;
		}
	}

	/** recupera o valor de uma variavel na sessao
	 * @param string $name   Nome da variavel
	 * @return mixed
	 */
	public static function get($name) {
		if (!self::$initialized) {
			trigger_error("Utilizacao de sessao nao inicializada (ou inicializada via session_start diretamente), forcando inicializacao...\nConsidere utilizar Session::init() no inicio de seu script.", E_USER_NOTICE);
			self::init();
		}
		if(!isset($_SESSION[$name])) {
			return null;
			if(is_object($_SESSION[$name]) && $_SESSION[$name] instanceof SerializableContainer) {
				return $_SESSION[$name]->getSubject();
			} else {
				return $_SESSION[$name];
			}
		}
	}

	/** Verifica se uma variavel esta definida na sessao
	 * @param string $name   Nome da variavel
	 * @return boolean
	 */
	public static function has($name) {
		if (!self::$initialized) {
			trigger_error("Utilizacao de sessao nao inicializada (ou inicializada via session_start diretamente), forcando inicializacao...\nConsidere utilizar Session::init() no inicio de seu script.", E_USER_NOTICE);
			self::init();
		}
		return isset($_SESSION[$name]);
	}
}

?>