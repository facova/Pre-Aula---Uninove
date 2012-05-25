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
 * File: MySQLDriver.php
 **/

import('engine.db.drivers.DbDriver');

/** Driver para MySQL
 * @author Silas R. N. Junior
 */
class MySQLDriver extends DbDriver {

	/** Tipo do driver
	 * @var string
	 */
	private $type = "mysql";

	/** Caractere delimitador de nome de tabela
	 * @var string
	 */
	private $tableFormatChar = "`";

	/** Caractere delimitador de nome de coluna
	 * @var string
	 */
	private $fieldFormatChar = "`";

	/** Caractere delimitador de nome de string
	 * @var string
	 */
	private $stringFormatChar = "'";

	/** Caractere que define o boolean true
	 * @var string
	 */
	private $trueChar = "1";

	/** Caractere que define o boolean false
	 * @var string
	 */
	private $falseChar = "0";

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
			case 'datetime':
				if ($value == '')
				return 'null';
				else
				return 'STR_TO_DATE('.$this->getStringDelimiter().$value.$this->getStringDelimiter().',\'%d/%m/%Y %H:%i:%s\')';
				break;
			case 'date':
				if ($value == '')
				return 'null';
				else
				return 'STR_TO_DATE('.$this->getStringDelimiter().$value.$this->getStringDelimiter().',\'%d/%m/%Y\')';
				break;
			case 'boolean':
				if ($value == $this->getTrueChar())
				$return = $this->getTrueChar();
				else
				$return = $this->getFalseChar();
				return $return;
				break;
			case 'binary':
				if ($value == '')
				return 'null';
				else
				return $this->getStringDelimiter().addslashes($value).$this->getStringDelimiter();
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
			
		throw new DbDriverException("[MySQLDriver] Tipo desconhecido ao formatar valor [Tipo: $type, Valor: $value].");
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
		$id = mysql_insert_id($this->getCon());
		return (int)$id;
	}

	/** Abre a conexao com o banco
	 * @return void
	 */
	public function connect() {
		$this->setCon(@mysql_connect($this->getHost(), $this->getUser() ,$this->getPass()));
		if(!$this->getCon()) {
			if(defined('ENGINE_DEBUG_LOG')) {
				Logger::getInstance()->add("[MySQLDriver] Erro ao conectar no banco de dados do sistema. Verifique os parametros fornecidos ao criar o driver ou se o banco de dados esta aceitando conexoes.");
			}
			throw new DbDriverConnectionException("[MySQLDriver] Erro ao conectar no banco de dados do sistema. Verifique os parametros fornecidos ao criar o driver ou se o banco de dados esta aceitando conexoes.");
		}
		if(!mysql_selectdb($this->getDatabase() ,$this->getCon())) {
			if(defined('ENGINE_DEBUG_LOG')) {
				Logger::getInstance()->add("[MySQLDriver] Erro ao selecionar base de dados do sistema. Verifique os parametros fornecidos ao criar o driver ou se a base de dados esta criada e/ou se o usuario tem permissao de acesso.");
			}
			throw new DbDriverException("[MySQLDriver] Erro ao selecionar base de dados do sistema. Verifique os parametros fornecidos ao criar o driver ou se a base de dados esta criada e/ou se o usuario tem permissao de acesso.");
		}
		mysql_set_charset('utf8', $this->getCon());
	}

	/** Fecha a conexao com o banco
	 * @return void
	 */
	public function disconnect() {
		@mysql_close();
	}

	/** Executa uma query
	 * @param String $sql   Query SQL
	 * @return resource
	 */
	public function run($sql) {
		if(defined('ENGINE_DEBUG_LOG')) {
			Logger::getInstance()->add($sql);
		}
		$aux = @mysql_query($sql,$this->getCon());
		if(!$aux) {
			$debugData = '';
			$err = mysql_error($this->getCon());
			//echo "Erro>".$err."\n";
			if(defined('ENGINE_DEBUG_VERBOSE')) {
				if (ENGINE_DEBUG_VERBOSE > 5) {
					$debugData = "<br>Query: <b>".$sql."</b><br><i>Erro:</i> ".$err;
				} else {
					$debugData = "<br>Erro: ".$err;
				}
			}
			if(defined('ENGINE_DEBUG_LOG')) {
				Logger::getInstance()->add("[MySQLDriver] Falha ao executar um comando no Banco de Dados.".$debugData);
			}
			//Processar erros comuns
			$pattern = "/Table '(?P<table>([\w\d\.]*))' doesn't exist/";
			preg_match_all($pattern,$err,$matches);
			if (count($matches['table']) > 0) {
				$datatable = explode(".",$matches['table'][0]);
				$e =  new TableNotFoundException("[MySQLDriver] A tabela [".$datatable[1]."] nao existe.".$debugData);
				$e->setDatabase($datatable[0]);
				$e->setTable($datatable[1]);
				throw $e;
			}
			
			if (stristr($sql,"INSERT")) {
				$pattern = "/INSERT INTO \\".$this->getTableDelimiter()."(?P<table>([\w\d_]*))\\".$this->getTableDelimiter()." \(/";
			} else if (stristr($sql,"UPDATE")) {
				$pattern = "/UPDATE \\".$this->getTableDelimiter()."(?P<table>([\w\d_]*))\\".$this->getTableDelimiter()." SET/";
			}
			preg_match_all($pattern,$sql,$matches);
			if (count($matches['table']) > 0) {
				$table = $matches['table'][0];
			}
			$pattern = "/Unknown column '(?P<column>([\w\d\.]*))'/";
			preg_match_all($pattern,$err,$matches);
			if (count($matches['column']) > 0) {
				if (count($matches['column']) == 1) {
					$matches['column'] = explode(".",$matches['column'][0]);
					$e = new ColumnNotFoundException("[MySQLDriver] A coluna [".$table.".".$matches['column'][sizeof($matches['column']) - 1]."] nao existe.".$debugData);
					$e->setAlias($matches['column'][0]);
					$e->setColumn($matches['column'][1]);
					throw $e;
				} else {
					$e = new ColumnNotFoundException("[MySQLDriver] A coluna [".$table.".".$matches['column'][0]."] nao existe.".$debugData);
					$e->setColumn($matches['column'][0]);
					throw $e;
				}
			}
			$pattern = "/Field '(?P<field>([\w\d]*))' doesn't have a default value/";
			preg_match_all($pattern,$err,$matches);
			if (count($matches['field']) > 0) {
				$e =  new NullFieldException("[MySQLDriver] A coluna [".$matches['field'][0]."] nao deve possuir valores nulos.".$debugData);
				$e->setColumn($matches['field'][0]);
				throw $e;
			}

			$pattern = "/Duplicate entry '([\w\d]*)' for key (?P<indexIdx>([\d]*))/";
			preg_match_all($pattern,$err,$matches);
			if (count($matches['indexIdx']) > 0) {
				$indexIdx = $matches['indexIdx'][0];
				if (stristr($sql,"INSERT")) {
					$pattern = "/INSERT INTO \\".$this->getTableDelimiter()."(?P<table>([\w\d_]*))\\".$this->getTableDelimiter()." \(/";
				} else if (stristr($sql,"UPDATE")) {
					$pattern = "/UPDATE \\".$this->getTableDelimiter()."(?P<table>([\w\d_]*))\\".$this->getTableDelimiter()." SET/";
				}
				preg_match_all($pattern,$sql,$matches);
				if (count($matches['table']) > 0) {
					$table = $matches['table'][0];
					$auxSql = "SHOW INDEX FROM ".$this->getTableDelimiter().$table.$this->getTableDelimiter();
					$indexes = $this->fetchAssoc($auxSql);
					$indexName = $indexes[$indexIdx-1]['Key_name'];
					$e = new ConstraintException("[MySQLDriver] A constraint de nome {$indexName} foi violada.");
					$e->setConstraint($indexName);
					$e->setType(ConstraintException::UNIQUE);
					throw $e;

				}
			}

			throw new SQLException("[MySQLDriver] Falha ao executar um comando no Banco de Dados.".$debugData);
			return false;
		} else {
			if (in_array(substr($sql, 0, 6), array("INSERT","UPDATE","DELETE"))) {
				//Workaraound para updates com os mesmos dados.
				//@author Ome Ko @ php.net - http://php.net/manual/pt_BR/function.mysql-affected-rows.php
				$_kaBoom=explode(' ',mysql_info());
				if (isset($_kaBoom[2])) {
   					$mysql_affected_rows = $_kaBoom[2];
				} else {
					$mysql_affected_rows = 0;
				}
   				
				if (($mysql_affected_rows == 0)&&(mysql_affected_rows($this->getCon()) == 0)) {
					if(defined('ENGINE_DEBUG_VERBOSE')) {
						if (ENGINE_DEBUG_VERBOSE > 5) {
							$debugData = "<br>Query: <b>".$sql."</b><br><i>Erro:</i> ".$err;
						} else {
							$debugData = "";
						}
					}
					throw new NoRecordsAffectedException("[MySQLDriver] A operacao nao afetou nenhum registro no banco de dados [".$debugData."]");
				}
			}
			$this->setRes($aux);
			return $aux;
		}
	}

	/** Obtem o resultado em um array associativo
	 * @param String $sql   Query SQL
	 * @return array
	 */
	public function fetchAssoc($sql) {
		try {
			$result = $this->run($sql);
			$count = mysql_num_rows($result);
			$rows = array();
			$row = "";
			for ($i = 0; $i < $count; $i++) {
				$rows[] = @mysql_fetch_assoc($result);
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
		return "UPPER(".$name.")";
	}
	
	/** Aplica a fucao de caixa baixa do banco
	 * @param string $name
	 */
	public function toLower($name) {
		return "LOWER(".$name.")";
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

	/** Define o limite e o offset dos resultados da query
	 * @param int $rows   Maximo numero de resultados da query
	 * @param int $offset   Numero de resultados a serem pulados.
	 * @return string
	 */
	public function limit($rows, $offset = null) {
		if (!$rows) {
			throw new DbDriverException("[MySQLDriver] Parâmetro rows obrigatório");
		}

		if ($offset) {
			$sql = " LIMIT ".$offset." , ".$rows;
		} else {
			$sql = " LIMIT ".$rows;
		}
		return $sql;
	}
}

?>