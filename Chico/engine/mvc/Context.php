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
 * File: Context.php
 **/


/** Armazena dados sobre um determinado contexto visando encapsular os dados de uma aplicacao na sessao.
 * @author Silas R. N. Junior
 */
class Context {

	/** Identificador do Contexto
	 * @var string
	 */
	private $id;

	/** Array de dados do contexto
	 * @var array
	 */
	private $data = array();

	/**
	 * @param string $id   Identificacao do contexto
	 */
	public function Context($id) {
		$this->id = $id;
	}

	/** Retorna o identificador do contexto
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/** Armazena uma variavel no contexto
	 * @param string $name   Nome da variavel
	 * @param mixed $value   Valor da variavel
	 * @return void
	 */
	public function setVar($name, $value) {
		if (is_null($value)) {
			$this->data[$name] = null;
			unset($this->data[$name]);
		} else {
			$this->data[$name] = $value;
		}
	}

	/** Recupera o valor de uma variavel no contexto
	 * @param string $name   Nome da variavel
	 * @return mixed
	 */
	public function getVar($name) {
		return $this->data[$name];
	}

	public function __call($m,$a) {
		if (stristr($m,'get')) {
			$str = str_replace('get','',$m);
			$str[0] = strtolower($str[0]);
			return $this->data[(string)$str];
		}
	}

}

?>