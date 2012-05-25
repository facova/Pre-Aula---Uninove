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
 * @subpackage db
 * File: DAO.php
 **/

import('engine.db.ORM');
import('engine.db.ORMRequest');
import('engine.db.ORMBuilder');
import('engine.db.ORMLoader');
import('engine.db.ORMLoaderBuilder');
import('engine.db.ORMPersister');
import('engine.db.ORMPersisterBuilder');
import('engine.db.ORMDeleter');
import('engine.db.ORMDeleterBuilder');
import('engine.db.EntityFilter');
import('engine.mvc.Request');

/** Data Access Object - Classe de Relacionamento Entidade X Banco
 * @author Silas R. N. Junior
 */
class DAO {

	/** Driver do banco de dados
	 * @var IDbDriver
	 */
	private $driver;

	/** Flag de transacao em andamento
	 * @var boolean
	 */
	private $ongoingTransaction;

	/** Classe de acesso a dados
	 * @param DbDriver $driver   Driver do banco de dados
	 */
	public function DAO(DbDriver $driver) {
		$this->driver = $driver;
	}

	/** Recupera no banco de dados a entidade informada.
	 * @param object $entity   Entidade a ser recuperada do banco [atributo chave deve estar setado e as entidades componentes a serem recuperadas devem estar definidas]
	 * @return boolean
	 */
	public function load(&$entity) {
		try {
			
			if ($entity instanceof IEntityContainer) {
				$entity->init();
				$entity = $entity->getSubject();
				return true;
			}
			
			$builder = new ORMLoaderBuilder(true);
			if (!$this->isOngoingTransaction()) {
				$this->getDriver()->connect();
			}
			if (!is_array($entity)) {
				$arr = array(&$entity);
			} else {
				$arr = &$entity;
			}

			$className = get_class($arr[0]); //verificar se todos os objetos sao instancia da mesma classe
			
			foreach ($arr as $en) {
				if ($className != get_class($en))
				throw new Exception("[DAO] Somente um array contendo objetos instancia da mesma classe pode ser fornecido.");
			}
			
			if (func_num_args() > 1) {
				$entityFilter = func_get_arg(1);
			} else {
				$entityFilter = null;
			}
			
			$builder->build($className);
			$loader = $builder->getRoot();
			foreach( $arr as &$object ) {
				$request = new ORMRequest($this->getDriver(),$object,$entityFilter);
				$loader->sync($request);
			}
			unset($request);
			unset($builder);
			unset($loader);
		} catch (DbException $e) {
			if ($e->getMessage() == "Load Retornou 0 Registros") {
				return false;
			}
			$this->getDriver()->disconnect();
			throw $e;
		} catch (Exception $e) {
			$this->getDriver()->disconnect();
			if(defined('ENGINE_DEBUG_NO_TRACE')) {
				throw new Exception("Erro carregando a entidade. \n [".$e->getMessage()."]\n");
			} else {
				throw new Exception("Erro carregando a entidade. \n [".$e->getMessage()."]\n\n".$e->getTraceAsString()."]\n");
			}
		}
		if (!$this->isOngoingTransaction()) {
			$this->getDriver()->disconnect();
		}
		return true;
	}

	/** Grava ou atualiza a entidade informada
	 * @param object $entity   Entidade a ser gravada/atualizada no banco [atributo chave deve estar setado para atualizacao ou um novo registro sera criado. As entidades componentes a serem gravadas/atualizadas devem estar definidas]
	 * @return void
	 */
	public function save(&$entity) {
		try {
			$builder = new ORMPersisterBuilder();
			if (!$this->isOngoingTransaction()) {
				$this->getDriver()->connect();
				$this->getDriver()->begin();
			}
			if (!is_array($entity)) {
				$arr = array(&$entity);
			} else {
				$arr = &$entity;
			}

			$className = get_class($arr[0]); //verificar se todos os objetos sao instancia da mesma classe
			
			foreach ($arr as $en) {
				if ($className != get_class($en))
				throw new Exception("[DAO] Somente um array contendo objetos instancia da mesma classe pode ser fornecido.");
			}

			$builder->build($className);
			$persister = $builder->getRoot();
			foreach( $arr as &$object ) {
				$persister->sync(new ORMRequest($this->getDriver(), $object));
			}
			
			unset($request);
			unset($builder);
			unset($persister);
		} catch (DbDriverConnectionException $e) {
			$this->setOngoingTransaction(false);
			throw $e;
		} catch (DbException $e) {
			if (!$this->isOngoingTransaction()) {
				$this->getDriver()->rollback();
				$this->getDriver()->disconnect();
			}
			throw $e;
		} catch (ConstraintException $e) {
			if (!$this->isOngoingTransaction()) {
				$this->getDriver()->rollback();
				$this->getDriver()->disconnect();
			}
			throw $e;
		} catch (Exception $e) {
			if (!$this->isOngoingTransaction()) {
				$this->getDriver()->rollback();
				$this->getDriver()->disconnect();
			}
			if(defined('ENGINE_DEBUG_NO_TRACE')) {
				throw new Exception("Erro persistindo a entidade. \n [".$e->getMessage()."]\n");
			} else {
				throw new Exception("Erro persistindo a entidade. \n [".$e->getMessage()."\n\n".$e->getTraceAsString()."]\n");
			}
		}
		if (!$this->isOngoingTransaction()) {
			$this->getDriver()->commit();
			$this->getDriver()->disconnect();
		}
	}

	/** Exclui a entidade informada
	 * @param object $entity   Entidade a ser excluida do banco [atributo chave deve estar setado e as entidades componentes a serem excluidas devem estar definidas]
	 * @return void
	 */
	public function delete(&$entity) {
		try {
			$builder = new ORMDeleterBuilder();
			if (!$this->isOngoingTransaction()) {
				$this->getDriver()->connect();
				$this->getDriver()->begin();
			}
			if (!is_array($entity)) {
				$arr = array(&$entity);
			} else {
				$arr = &$entity;
			}

			$className = get_class($arr[0]); //verificar se todos os objetos sao instancia da mesma classe
			
			foreach ($arr as $en) {
				if ($className != get_class($en))
				throw new Exception("[DAO] Somente um array contendo objetos instancia da mesma classe pode ser fornecido.");
			}

			$builder->build($className);
			$deleter = $builder->getRoot();
			foreach( $arr as &$object ) {
				$deleter->sync(new ORMRequest($this->getDriver(),$object));
			}
			
			unset($request);
			unset($builder);
			unset($deleter);
		} catch (DbDriverConnectionException $e) {
			$this->setOngoingTransaction(false);
			throw $e;
		} catch (DbException $e) {
			if (!$this->isOngoingTransaction()) {
				$this->getDriver()->rollback();
				$this->getDriver()->disconnect();
			}
			throw $e;
		} catch (Exception $e) {
			if (!$this->isOngoingTransaction()) {
				$this->getDriver()->rollback();
				$this->getDriver()->disconnect();
			}
			if(defined('ENGINE_DEBUG_NO_TRACE')) {
				throw new Exception("Erro excluindo entidade. \n [".$e->getMessage()."]\n");
			} else {
				throw new Exception("Erro excluindo entidade. \n [".$e->getMessage()."\n\n".$e->getTraceAsString()."]\n");
			} 
		}
		if (!$this->isOngoingTransaction()) {
			$this->getDriver()->commit();
			$this->getDriver()->disconnect();
		}
	}

	/** inicia uma transacao. Essa operacao se sobrepoe ao controle automatico de transacao por entidade.
	 * @return void
	 */
	public function beginTransaction() {
		try {
			if ($this->isOngoingTransaction()) {
				self::rollbackTransaction();
				self::endTransaction();
				throw new Exception("[DAO] Existe uma transacao em andamento");
			}
			$this->setOngoingTransaction(true);
			$this->getDriver()->connect();
			$this->getDriver()->begin();
		} catch (Exception $e) {
			self::endTransaction();
			die($e->getMessage());
		}
	}

	/** Desfaz as operacoes de uma transacao. Essa operacao se sobrepoe ao controle automatico de transacao por entidade.
	 * @return void
	 */
	public function rollbackTransaction() {
		try {
			$this->getDriver()->rollback();
			$this->setOngoingTransaction(false);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/** Confirma uma transacao. Essa operacao se sobrepoe ao controle automatico de transacao por entidade.
	 * @return void
	 */
	public function commitTransaction() {
		try {
			if (!$this->isOngoingTransaction()) {
				throw new Exception("[DAO] Nao existe uma transacao em andamento");
			}
			$this->setOngoingTransaction(false);
			$this->getDriver()->commit();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/** Termina uma transacao. Essa operacao se sobrepoe ao controle automatico de transacao por entidade.
	 * @return void
	 */
	public function endTransaction() {
		try {
			$this->setOngoingTransaction(false);
			$this->getDriver()->disconnect();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/** Retorna o Driver
	 * @return DbDriver
	 */
	public function getDriver() {
		if (!isset($this->driver)) {
			throw new Exception("[DAO] O Driver de banco de dados nao foi fornecido.");
		}
		return $this->driver;
	}

	/** Define o driver
	 * @param DbDriver $newDriver
	 * @return void
	 */
	public function setDriver(DbDriver $newDriver) {
		$this->driver = $newDriver;
	}

	function __destruct() {
		if ($this->isOngoingTransaction()) {
			trigger_error("O DAO foi destruido com uma transacao em andamento.", E_USER_NOTICE);
		}
		if(defined('ENGINE_DEBUG_LOG')) {
			if(defined('ENGINE_DEBUG_VERBOSE')) {
				if (ENGINE_DEBUG_VERBOSE > 5) {
					Logger::getInstance()->add("Destruindo DAO");
				}
			}
		}
	}

	/** Retorna um filtro para a classe fornecida
	 * @param string $className   Nome da classe
	 * @return EntityFilter
	 */
	public function getFilterFor($className) {
		return new EntityFilter($this,$className);
	}

	/** Verifica se existe uma transacao em andamento neste DAO
	 * @return boolean
	 */
	public function isOngoingTransaction() {
		return $this->ongoingTransaction;
	}

	/** Define se existe uma transacao em andamento neste DAO
	 * @param boolean $newOngoingTransaction
	 * @return void
	 */
	private function setOngoingTransaction($newOngoingTransaction) {
		$this->ongoingTransaction = $newOngoingTransaction;
	}
}
?>