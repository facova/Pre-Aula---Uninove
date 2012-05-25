<?php
/**
 * Engine PHP Application Framework
 * http://seelaz.com.br
 *
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
 * @subpackage db
 * File: ORM.php
 **/


/** Objeto responsavel pelo gerenciamento de operacoes ORM
 * @author Silas R. N. Junior
 */
abstract class ORM {

	/** Apelido da entidade mapeada
	 * @var string
	 */
	private $alias;

	/** Dados de Mapeamento de entidade
	 * @var ReflectionORMClass
	 */
	private $rflctORM;

	/** Proximo mapeamento de entidade
	 * @var ORM
	 */
	private $next;

	/** Caminho do mapeamento
	 * @var string
	 */
	private $path;

	/** SQL Query em cache
	 * @var string
	 */
	private $cached;

	/** Associacoes entre tabelas
	 * @var array
	 */
	protected $associations = array();

	/** Profundidade do relacionamento
	 * @var int
	 */
	private $depth;


	/** Define o proximo mapeamento
	 * @param ORM $newNext   Proximo mapeamento
	 * @return void
	 */
	public function setNext(ORM $newNext) {
		if ($this->getNext()) {
			$this->getNext()->setNext($newNext);
		} else {
			$this->next = $newNext;
		}
	}

	/** retorna o proximo mapeamento
	 * @return ORM
	 */
	public function getNext() {
		return $this->next;
	}

	/** Retorna a entidade a qual o mapeamento se refere
	 * @param object $o   Entidade persistente
	 * @param boolean $initialize   Cria o objeto da entidade caso nao exista
	 * @return object
	 */
	public function &fetchEntity(&$o, $initialize = false) {
		$null = null;
		try {
			if ($this->path != "root#") {
				$proxy = new ORMProxy($o,$initialize);
				$method = str_replace("root#.","",$this->path);
				$value = $proxy->{$method};
				if (!is_null($value)) {
					return $value;
				} else {
					return $null;
				}
			} else {
				return $o;
			}
		} catch (ReflectionException $e) {
			throw $e;
		} catch (Exception $e) {
			return null;
		}
	}

	/** Define a entidade a qual o mapeamento se refere
	 * @param object $o
	 * @return void
	 */
	public function setEntity(&$entity, &$object, $initialize = false) {
		if ($this->path != "root#") {
			$proxy = new ORMProxy($entity,$initialize);
			$proxy->{str_replace("root#.","",$this->path)} = $object;
		} else {
			$entity = $object;
		}
	}

	/** Retorna a classe de reflexao da entidade
	 * @return ReflectionORMClass
	 */
	public function getRflctORM() {
		return $this->rflctORM;
	}

	/** Define a classe de reflexao
	 * @param ReflectionORMClass $newRflctORM    Objeto da classe de reflexao
	 * @return void
	 */
	protected function setRflctORM(ReflectionORMClass $newRflctORM) {
		$this->rflctORM = $newRflctORM;
	}

	/** Executa a sincronia da(s) entidade(as) com o banco de dados
	 * @param ORMRequest $request    Requisicao ORM
	 * @return void
	 */
	public abstract function sync(ORMRequest $request);

	/** Retorna o alias do mapeamento
	 * @return string
	 */
	public function getAlias() {
		return "TB".sprintf("%03d",$this->alias);//$this->alias;
	}

	/** Define o alias do mapeamento
	 * @param string $newAlias
	 * @return void
	 */
	public function setAlias($newAlias) {
		$this->alias = $newAlias;
	}

	/** Obtem a query em cache
	 * @return string
	 */
	public function getCached() {
		return $this->cached;
	}

	/** Define a query no cache
	 * @param string $newCached    SQL Query
	 * @return void
	 */
	public function setCached($newCached) {
		$this->cached = $newCached;
	}

	/** Retona o path da entidade na associacao
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/** Define o path da entidade na associacao
	 * @param string $newPath
	 * @return void
	 */
	protected function setPath($newPath) {
		$this->path = $newPath;
	}

	/** Adicionar uma associacao
	 * @param ORMAssociation $ormAssociation
	 */
	public function addAssociation(ORMAssociation $ormAssociation) {
		$this->associations[] = $ormAssociation;
	}

	public function getAssociations() {
		return $this->associations;
	}

	/** Constroi a string SQL
	 * @param DbDriver $driver    Driver do banco de dados
	 * @param object $entity    Objeto da entidade
	 * @return void
	 */
	public abstract function buildSQL(ORMRequest $request);

	/** Obtem a profundidade atual (relacionamentos recursivos)
	 * @return int
	 */
	public function getDepth() {
		return $this->depth;
	}

	/** Define a profundidade atual (relacionamentos recursivos)
	 * @param int $newDepth
	 * @return void
	 */
	public function setDepth($newDepth) {
		$this->depth = $newDepth;
	}
}

?>
