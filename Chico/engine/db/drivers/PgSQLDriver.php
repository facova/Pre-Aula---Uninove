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
 * File: PgSQLDriver.php
 **/

import('engine.db.drivers.DbDriver');

/** Driver para PostgreSQL
 * @author Silas R. N. Junior
 */
class PgSQLDriver extends DbDriver {

	/** Tipo do driver
	 * @var string
	 */
	private $type = "pgsql";

	/** Caractere delimitador de nome de tabela
	 * @var string
	 */
	private $tableFormatChar = "\"";

	/** Caractere delimitador de nome de coluna
	 * @var string
	 */
	private $fieldFormatChar = "\"";

	/** Caractere delimitador de nome de string
	 * @var string
	 */
	private $stringFormatChar = "'";

	/** Caractere que define o boolean true
	 * @var string
	 */
	private $trueChar = "t";

	/** Caractere que define o boolean false
	 * @var string
	 */
	private $falseChar = "f";

	/** PostgreSQL result resource
	 * @var object
	 */
	private $resource;

	/** Retorna o caractere que delimita nomes de tabela na string da query
	 * @return string
	 */
	public function getTableDelimiter() {
		return $this->tableFormatChar;
	}

	/** Retorna o caractere que delimita nomes de campos na string da query
	 * @return string
	 */
	public function getFieldDelimiter() {
		return $this->fieldFormatChar;
	}

	/** Retorna o caractere que delimita valores tipo string na string da query
	 * @return string
	 */
	public function getStringDelimiter() {
		return $this->stringFormatChar;
	}

	/** Retorna o caractere que define tipo true na string da query
	 * @return string
	 */
	public function getTrueChar() {
		return $this->trueChar;
	}

	/** Retorna o caractere que define tipo false na string da query
	 * @return string
	 */
	public function getFalseChar() {
		return $this->falseChar;
	}

	/** Retorna o valor formatado de acordo com seu tipo para uso na string da query
	 * @param String $type
	 * @param Object $value
	 * @return string
	 */
	public function formatValue($type, $value) {
		switch (strtolower($type)) {
			case 'string':
				//case 'time':
				return $this->getStringDelimiter().addslashes($value).$this->getStringDelimiter();
				break;
			case 'integer':
			case 'int':
				return $value;
				break;
			case 'real':
			case 'float':
			case 'long':
			case 'money':
				$v = str_replace(",",".",$value);
				//return number_format($value,2,'.','');
				return $v;
				break;
			case 'time':
				if ($value == '')
				return 'null';
				else
				return $this->getStringDelimiter().addslashes($value).$this->getStringDelimiter();
				break;
			case 'date':
				if ($value == '')
				return 'null';
				else
				return $this->getStringDelimiter().$value.$this->getStringDelimiter();
				break;
			case 'datetime':
				if ($value == '')
				return 'null';
				else
				return 'STR_TO_DATE('.$this->getStringDelimiter().$value.$this->getStringDelimiter().',\'%d/%m/%Y %H:%i:%s\')';
				break;
			case 'binary':
				if ($value == '')
				return 'null';
				else
				return $this->getStringDelimiter().addslashes($value).$this->getStringDelimiter();
				break;
			case 'boolean':
				if ($value == $this->getTrueChar())
				$return = $this->getStringDelimiter().$this->getTrueChar().$this->getStringDelimiter();
				else
				$return = $this->getStringDelimiter().$this->getFalseChar().$this->getStringDelimiter();
				return $return;
				break;
			case 'null':
				return 'null';
				break;
			default:
				return $value;
				break;
		}
		if (is_null($value))
		return $value;
			
		throw new DbDriverException("Tipo desconhecido ao formatar valor [Tipo: $type, Valor: $value].");
	}

	/** Retorna o valor formatado de acordo com seu tipo para uso no objeto
	 * @param String $type
	 * @param String $value
	 * @return Object
	 */
	public function unformatValue($type, $value) {
		switch ($type) {
			case 'string':
			case 'binary':
			case 'integer':
			case 'int':
				return $value;
				break;
			case 'real':
			case 'float':
			case 'long':
			case 'money':
				$v = str_replace(".",",",$value);
				return $v;
				break;
			case 'datetime':
				if (!is_null($value) && $value != "")
				return date("d/m/Y H:i:s",strtotime($value));
				break;
			case 'date':
				if (!is_null($value) && $value != "")
				return date("d/m/Y",strtotime($value));
				break;
			case 'time':
				if (!is_null($value) && $value != "")
				return date("H:i:s",strtotime($value));
				break;
			case 'boolean':
				if ($value == $this->trueChar)
				return true;
				else
				return false;
			default:
				return $value;
				break;
		}
	}

	/** Retorna o nome da tabela formatado para uso na string da query
	 * @param String $name
	 * @return string
	 */
	public function formatTable($name) {
		return $this->getTableDelimiter().$name.$this->getTableDelimiter();
	}

	/** Retorna o nome da coluna formatado para uso na string da query
	 * @param String $name
	 * @return string
	 */
	public function formatField($name) {
		return $this->getFieldDelimiter().$name.$this->getFieldDelimiter();
	}

	/** Define os parametros de conexao do Driver
	 * @param String $host   Endereco do host do servidor
	 * @param String $database   Nome do banco de dados no servidor
	 * @param String $user   Nome do usuario do banco
	 * @param String $password   Senha do usuario do banco
	 * @return DbDriver
	 */
	public function configure($host, $database, $user, $password) {
		$this->setHost($host);
		$this->setUser($user);
		$this->setPass($password);
		$this->setDatabase($database);
		return $this;
	}

	/** Retorna o ultimo valor inserido do indice na tabela
	 * @param ReflectionORMProperty $index    Propriedade do indice
	 * @return int
	 */
	public function getLastInserted(ReflectionORMProperty $index = null) {

		if ($index->getGenerationStrategy() != GenerationType::AUTO) continue;
		$seqName = $this->formatField($index->getDeclaringORMClass()->getTableName()."_".$index->getColumnName()."_seq");
			//currval
			
		$column = "currval('".$seqName."') AS ".$this->formatField($index->getColumnName());
		// returning >=8.2
		//$sql = "SELECT ";
		// sequence
		//$sql = "SELECT (CASE WHEN is_called THEN last_value ELSE last_value-increment_by END) AS {$index->getColumnName()} FROM ".$this->formatField($orm->getTableName()."_".$index->getColumnName()."_seq");
		// currval
		//$sql = "SELECT currval(".$this->formatField($orm->getTableName()."_".$index->getColumnName()."_seq").")";
		$sql = "SELECT ".$column;
		// oid
		//$oid = pg_last_oid($this->getRes());
		//$sql = "SELECT {$this->formatField($idx[0])} FROM {$this->formatTable($queuedEntity->getTable()->getName())} WHERE oid = ".$oid;
		//echo "SQL GERADA:".$sql."\n";
		try {
			$data = $this->fetchAssoc($sql);
		}catch (Exception $e) {
			throw new DbDriverException($e);
		}
		return $data[0][$index->getColumnName()];

	}

	/** Abre a conexao com o banco
	 * @return void
	 */
	public function connect() {
		$this->setCon(@pg_connect("host={$this->getHost()} port=5432 dbname={$this->getDatabase()} user={$this->getUser()} password={$this->getPass()}"));
		if(!$this->getCon()) {
			if(defined('ENGINE_DEBUG_LOG')) {
				Logger::getInstance()->add("[PgSQLDriver] Erro ao conectar no banco de dados do sistema. Verifique os parametros fornecidos ao criar o driver ou se o banco de dados esta aceitando conexoes.");
			}
			throw new DbDriverConnectionException("[PgSQLDriver] Erro ao conectar no banco de dados do sistema. Verifique os parametros fornecidos ao criar o driver ou se o banco de dados esta aceitando conexoes.");
		}
	}

	/** Fecha a conexao com o banco
	 * @return void
	 */
	public function disconnect() {
		@pg_close();
	}

	/** Executa uma query
	 * @param String $sql   Query SQL
	 * @return resource
	 */
	public function run($sql) {
		if(defined('ENGINE_DEBUG_LOG')) {
			Logger::getInstance()->add($sql);
		}
		$this->setRes(null);
		$aux = @pg_query($this->getCon(),$sql);
		if(!$aux) {
			$debugData = '';
			$err = pg_last_error($this->getCon());
			if(defined('ENGINE_DEBUG_VERBOSE')) {
				if (ENGINE_DEBUG_VERBOSE > 5) {
					$debugData = "<br>Query: <b>".$sql."</b><br><i>Erro:</i> ".$err;
				} else {
					$debugData = "<br>Erro: ".$err;
				}
			}
			if(defined('ENGINE_DEBUG_LOG')) {
				Logger::getInstance()->add("Falha ao executar um comando no Banco de Dados.".$debugData);
			}
			//Processar erros comuns

			$pattern = "/ERROR:  relation \"(?P<table>([\w\d]*))\" does not exist/";
			preg_match_all($pattern,$err,$matches);
			if (count($matches['table']) > 0) {
				$e =  new TableNotFoundException("[PgSQLDriver] A tabela [".$matches['table'][0]."] nao existe.".$debugData);
				$e->setDatabase($this->getDatabase());
				$e->setTable($matches['table'][0]);
				throw $e;
			}

			$pattern = "/ column \"(?P<column>([\w\d]*))\" of relation/";
			preg_match_all($pattern,$err,$matches);
			if (count($matches['column']) > 0) {
				if (count($matches['column']) > 1) {
					$matches['column'] = explode(".",$matches['column'][0]);
					$e = new ColumnNotFoundException("[PgSQLDriver] A coluna [".$matches['column'][1]."] nao existe.".$debugData);
					$e->setAlias($matches['column'][0]);
					$e->setColumn($matches['column'][1]);
					throw $e;
				} else {
					$e = new ColumnNotFoundException("[PgSQLDriver] A coluna [".$matches['column'][0]."] nao existe.".$debugData);
					$e->setColumn($matches['column'][0]);
					throw $e;
				}
			}

			$pattern = "/Field '(?P<field>([\w\d]*))' doesn't have a default value/";
			preg_match_all($pattern,$err,$matches);
			if (count($matches['field']) > 0) {
				$e =  new NullFieldException("[PgSQLDriver] A coluna [".$matches['field'][0]."] nao deve possuir valores nulos.".$debugData);
				$e->setColumn($matches['field'][0]);
				throw $e;
			}

			throw new SQLException("[PgSQLDriver] Falha ao executar um comando no Banco de Dados.".$debugData);
			return false;
		} else {
			if (in_array(substr($sql, 0, 6), array("INSERT","UPDATE","DELETE"))) {
				if (pg_affected_rows($aux) == 0) {
					if(defined('ENGINE_DEBUG_VERBOSE')) {
						if (ENGINE_DEBUG_VERBOSE > 5) {
							$debugData = "<br>Query: <b>".$sql."</b><br><i>Erro:</i> ".$err;
						} else {
							$debugData = "";
						}
					}
					throw new NoRecordsAffectedException("[PgSQLDriver] A operacao nao afetou nenhum registro no banco de dados [".$debugData."]");
				}
			}
			$this->setRes($aux);
			return $aux;
		}
	}

	/** Obtem o resultado em um array associativo
	 * @param String $sql   Query SQL
	 * @return string
	 */
	public function fetchAssoc($sql) {
		try {
			$result = $this->run($sql);
			$count = pg_num_rows($result);
			$rows = array();
			$row = "";
			for ($i = 0; $i < $count; $i++) {
				$rows[] = @pg_fetch_assoc($result);
			}
			$aux = $row ? $row : $rows;
		} catch (SQLException $e)	{
			throw $e;
		}
		return $aux;
	}
	
	/** Aplica a fucao de caixa alta do banco
	 * @param string $name
	 */
	public function toUpper($name) {
		return "upper(".$name.")";
	}
	
	/** Aplica a fucao de caixa baixa do banco
	 * @param string $name
	 */
	public function toLower($name) {
		return "lower(".$name.")";
	}

	/** Inicia uma transacao
	 * @return void
	 */
	public function begin() {
		$this->run("BEGIN");
	}

	/** Comita uma transacao
	 * @return void
	 */
	public function commit() {
		$this->run("COMMIT");
	}

	/** Desfaz as operacoes de uma transacao
	 * @return void
	 */
	public function rollback() {
		$this->run("ROLLBACK");
	}

	/** Retorna o codigo do driver
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/** Retorna o Result Resource
	 * @return object
	 */
	public function getResource() {
		return $this->resource;
	}

	/** Define o Result Resource
	 * @param object $newResource
	 * @return void
	 */
	public function setResource($newResource) {
		$this->resource = $newResource;
	}

	/** Define o limite e o offset dos resultados da query
	 * @param int $rows   Maximo numero de resultados da query
	 * @param int $offset   Numero de resultados a serem pulados.
	 * @return string
	 */
	public function limit($rows, $offset = null) {
		if (!$rows)
		throw new DbDriverException("Parâmetro rows obrigatório");

		$sql = " LIMIT ".$rows;
		if ($offset) {
			$sql .= " OFFSET ".$offset;
		}
		return $sql;
	}
}

?>