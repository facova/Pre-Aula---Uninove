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
 * File: AuthDummy.php
 **/

import('engine.mvc.IAuth');

/**
 * @author Silas R. N. Junior
 */
class AuthDummy implements IAuth {

	/** Verifica se as credenciais fornecidas sao validas
	 * @param string $user   Nome do usuario
	 * @param string $pass   Senha do usuario
	 * @return boolean
	 */
	public function login($user, $pass) {
		return true;
	}

	/** Desconecta o usuario da sessao
	 * @return boolean
	 */
	public function logout() {
		return true;
	}

	/** Verifica o acesso do usuario a um recurso ou acao
	 * @param string $uri   Endereco do recurso
	 * @return boolean
	 */
	public function access($uri) {
		return true;
	}

	public function isAuthenticated() {
		return true;
	}
}

?>
