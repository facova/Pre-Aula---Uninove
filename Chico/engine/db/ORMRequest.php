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
 * File: ORMRequest.php
 **/


/** Objeto de requisicao ORM
 * @author Silas R. N. Junior
 */
class ORMRequest {

	/** Driver do banco de dados
	 * @var DbDriver
	 */
	private $driver;

	/** Objeto da entidade
	 * @var object
	 */
	private $entity;

	/** Objeto de filtro para busca
	 * @var EntityFilter
	 */
	private $filter;

	/** Instancias processadas pela requisicao
	 * @var array
	 */
	private $processedEntities;

	/** Construtor
	 * @param DbDriver $driver
	 * @param object $entity
	 * @param EntityFilter $filter
	 * @param array $processed
	 */
	public function ORMRequest(DbDriver $driver, &$entity, EntityFilter $filter = null, &$processed = null) {
		$this->driver = $driver;
		$this->entity = &$entity;
		$this->filter = $filter;
		$this->processedEntities = $processed;
	}

	/**
	 * @return DbDriver
	 */
	public function getDriver() {
		return $this->driver;
	}

	/**
	 * @return object
	 */
	public function &getEntity() {
		return $this->entity;
	}

	/**
	 * @return EntityFilter
	 */
	public function getFilter() {
		return $this->filter;
	}

	/** Marca uma instancia como processada
	 * @param object $entity    Objeto da entidade
	 * @param string $className  Classe da entidade
	 * @return void
	 */
	public function setProcessed($entity, $className = null) {
		if (!is_null($entity) && is_object($entity)) {
			$this->processedEntities[spl_object_hash($entity).$className] = true;
		}
	}

	/** Verifica se a entidade ja foi processada
	 * @param object $entity    Objeto da entidade
	 * @param string $className  Classe da entidade
	 * @return boolean
	 */
	public function hasProcessed($entity, $className = null) {
		if (!is_null($entity) && is_object($entity)) {
			return isset($this->processedEntities[spl_object_hash($entity).$className]);
		}
	}

	/** Origina uma nova requisicao mantendo a lista do que foi feito
	 * @param object $entity    Entidade da requisicao
	 */
	public function getSubRequest(&$entity) {
		return new ORMRequest($this->driver, $entity,$this->filter, $this->processedEntities);
	}
}

?>
