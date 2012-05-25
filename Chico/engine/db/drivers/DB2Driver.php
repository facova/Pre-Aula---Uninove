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
 * File: DB2Driver.php
 **/

import('engine.db.drivers.DbDriver');

/** Driver para IBM DB2
 * @author Silas R. N. Junior
 */
class DB2Driver extends DbDriver {

	/** Retorna o caractere que delimita nomes de tabela na string da query
	 * @return char
	 */
	public function getTableDelimiter() {
		/** <TODO> Implement. */
	}

	/** Retorna o caractere que delimita nomes de campos na string da query
	 * @return char
	 */
	public function getFieldDelimiter() {
		/** <TODO> Implement. */
	}

	/** Retorna o caractere que delimita valores tipo string na string da query
	 * @return char
	 */
	public function getStringDelimiter() {
		/** <TODO> Implement. */
	}

	/** Retorna o caractere que define tipo true na string da query
	 * @return string
	 */
	public function getTrueChar() {
		/** <TODO> Implement. */
	}

	/** Retorna o caractere que define tipo false na string da query
	 * @return string
	 */
	public function getFalseChar() {
		/** <TODO> Implement. */
	}

	/** Retorna o valor formatado de acordo com seu tipo para uso na string da query
	 * @param String $type
	 * @param Object $value
	 * @return string
	 */
	public function formatValue($type, $value) {
		/** <TODO> Implement. */
	}

	/** Retorna o valor formatado de acordo com seu tipo para uso no objeto
	 * @param String $type
	 * @param String $value
	 * @return Object
	 */
	public function unformatValue($type, $value) {
		/** <TODO> Implement. */
	}

	/** Retorna o nome da tabela formatado para uso na string da query
	 * @param String $name
	 * @return string
	 */
	public function formatTable($name) {
		/** <TODO> Implement. */
	}

	/** Retorna o nome da coluna formatado para uso na string da query
	 * @param String $name
	 * @return string
	 */
	public function formatField($name) {
		/** <TODO> Implement. */
	}

	/** Define os parametros de conexao do Driver
	 * @param String $host   Endereco do host do servidor
	 * @param String $database   Nome do banco de dados no servidor
	 * @param String $user   Nome do usuario do banco
	 * @param String $password   Senha do usuario do banco
	 * @return DbDriver
	 */
	public function configure($host, $database, $user, $password) {
		/** <TODO> Implement. */
	}

	/** Retorna o ultimo valor inserido do indice na tabela
	 * @param ReflectionORMProperty $index    Propriedade do indice
	 * @return int
	 */
	public function getLastInserted(ReflectionORMProperty $index = null) {
		/** <TODO> Implement. */
	}

	/** Abre a conexao com o banco
	 * @return void
	 */
	public function connect() {
		/** <TODO> Implement. */
	}

	/** Fecha a conexao com o banco
	 * @return void
	 */
	public function disconnect() {
		/** <TODO> Implement. */
	}

	/** Executa uma query
	 * @param String $sql   Query SQL
	 * @return resource
	 */
	public function run($sql) {
		/** <TODO> Implement. */
	}

	/** Obtem o resultado em um array associativo
	 * @param String $sql   Query SQL
	 * @return string
	 */
	public function fetchAssoc($sql) {
		/** <TODO> Implement. */
	}
	
	/** Aplica a fucao de caixa alta do banco
	 * @param string $name
	 */
	public function toUpper($name) {
		/** <TODO> Implement. */
	}
	
	/** Aplica a fucao de caixa baixa do banco
	 * @param string $name
	 */
	public function toLower($name) {
		/** <TODO> Implement. */
	}

	/** Inicia uma transacao
	 * @return void
	 */
	public function begin() {
		/** <TODO> Implement. */
	}

	/** Comita uma transacao
	 * @return void
	 */
	public function commit() {
		/** <TODO> Implement. */
	}

	/** Desfaz as operacoes de uma transacao
	 * @return void
	 */
	public function rollback() {
		/** <TODO> Implement. */
	}

	/** Retorna o codigo do driver
	 * @return string
	 */
	public function getType() {
		/** <TODO> Implement. */
	}

	/** Define o limite e o offset dos resultados da query
	 * @param int $rows   Maximo numero de resultados da query
	 * @param int $offset   Numero de resultados a serem pulados.
	 * @return string
	 */
	public function limit($rows, $offset = null) {
		/** <TODO> Implement. */
	}
}

?>