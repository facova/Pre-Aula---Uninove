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
 * @package db
 * @subpackage drivers
 * File: DbDriver.php
 **/


/** Driver de banco de dados para o framework
 * @author Silas R. N. Junior
 */
abstract class DbDriver {

	const MYSQL = "mysql";
	const PGSQL = "pgsql";

	/** Endereco do servidor
	 * @var string
	 */
	protected $host;

	/** Banco de dados no servidor
	 * @var string
	 */
	protected $database;

	/** Usuario do servidor
	 * @var string
	 */
	protected $user;

	/** Senha do usuario
	 * @var string
	 */
	protected $pass;

	/** Recurso de conexao com o Banco
	 * @var object
	 */
	protected $con;

	/** Recurso de queries no banco
	 * @var object
	 */
	protected $res;

	/**
	 * @return string
	 */
	protected function getHost() {
		return $this->host;
	}

	/**
	 * @param string $newHost
	 * @return void
	 */
	protected function setHost($newHost) {
		$this->host = $newHost;
	}

	/**
	 * @return string
	 */
	protected function getDatabase() {
		return $this->database;
	}

	/**
	 * @param string $newDatabase
	 * @return void
	 */
	protected function setDatabase($newDatabase) {
		$this->database = $newDatabase;
	}

	/**
	 * @return string
	 */
	protected function getUser() {
		return $this->user;
	}

	/**
	 * @param string $newUser
	 * @return void
	 */
	protected function setUser($newUser) {
		$this->user = $newUser;
	}

	/**
	 * @return string
	 */
	protected function getPass() {
		return $this->pass;
	}

	/**
	 * @param string $newPass
	 * @return void
	 */
	protected function setPass($newPass) {
		$this->pass = $newPass;
	}

	/**
	 * @return object
	 */
	protected function getCon() {
		return $this->con;
	}

	/**
	 * @param object $newCon
	 * @return void
	 */
	protected function setCon($newCon) {
		$this->con = $newCon;
	}

	/**
	 * @return object
	 */
	protected function getRes() {
		return $this->res;
	}

	/**
	 * @param object $newRes
	 * @return void
	 */
	protected function setRes($newRes) {
		$this->res = $newRes;
	}

	/** Retorna o caractere que delimita nomes de tabela na string da query
	 * @return string
	 */
	public abstract function getTableDelimiter();

	/** Retorna o caractere que delimita nomes de campos na string da query
	 * @return string
	 */
	public abstract function getFieldDelimiter();

	/** Retorna o caractere que delimita valores tipo string na string da query
	 * @return string
	 */
	public abstract function getStringDelimiter();

	/** Retorna o caractere que define tipo true na string da query
	 * @return string
	 */
	public abstract function getTrueChar();

	/** Retorna o caractere que define tipo false na string da query
	 * @return string
	 */
	public abstract function getFalseChar();

	/** Retorna o valor formatado de acordo com seu tipo para uso na string da query
	 * @param String $type
	 * @param object $value
	 * @return string
	 */
	public abstract function formatValue($type, $value);

	/** Retorna o valor formatado de acordo com seu tipo para uso no objeto
	 * @param String $type
	 * @param String $value
	 * @return Object
	 */
	public abstract function unformatValue($type, $value);

	/** Retorna o nome da tabela formatado para uso na string da query
	 * @param String $name
	 * @return string
	 */
	public abstract function formatTable($name);

	/** Retorna o nome da coluna formatado para uso na string da query
	 * @param String $name
	 * @return string
	 */
	public abstract function formatField($name);

	/** Define os parametros de conexao do Driver
	 * @param String $host   Endereco do host do servidor
	 * @param String $database   Nome do banco de dados no servidor
	 * @param String $user   Nome do usuario do banco
	 * @param String $password   Senha do usuario do banco
	 * @return DbDriver
	 */
	public abstract function configure($host, $database, $user, $password);

	/** Retorna o ultimo valor inserido do indice na tabela
	 * @param ReflectionORMProperty $index    Propriedade do indice
	 * @return int
	 */
	public abstract function getLastInserted(ReflectionORMProperty $index = null);

	/** Abre a conexao com o banco
	 * @return void
	 */
	public abstract function connect();

	/** Fecha a conexao com o banco
	 * @return void
	 */
	public abstract function disconnect();

	/** Executa uma query
	 * @param String $sql   Query SQL
	 * @return resource
	 */
	public abstract function run($sql);

	/** Obtem o resultado em um array associativo
	 * @param String $sql   Query SQL
	 * @return array
	 */
	public abstract function fetchAssoc($sql);
	
	/** Aplica a fucao de caixa alta do banco
	 * @param string $name
	 */
	public abstract function toUpper($name);
	
	/** Aplica a fucao de caixa baixa do banco
	 * @param string $name
	 */
	public abstract function toLower($name);

	/** Inicia uma transacao
	 * @return void
	 */
	public abstract function begin();

	/** Comita uma transacao
	 * @return void
	 */
	public abstract function commit();

	/** Desfaz as operacoes de uma transacao
	 * @return void
	 */
	public abstract function rollback();

	/** Retorna o codigo do driver
	 * @return string
	 */
	public abstract function getType();

	/** Define o limite e o offset dos resultados da query
	 * @param int $rows   Maximo numero de resultados da query
	 * @param int $offset   Numero de resultados a serem pulados.
	 * @return string
	 */
	public abstract function limit($rows, $offset = null);

	function __destruct() {
		if(defined('ENGINE_DEBUG_LOG')) {
			if(defined('ENGINE_DEBUG_VERBOSE')) {
				if (ENGINE_DEBUG_VERBOSE > 5) {
					Logger::getInstance()->add("Destruindo DbDriver");
				}
			}
		}
	}
}

?>