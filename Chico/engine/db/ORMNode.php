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
 * File: ORMNode.php
 **/


/** Classe de navegacao dos relacionamentos da entidade
 * @author Silas R. N. Junior
 */
class ORMNode {

	/** Classe de reflexao da entidade
	 * @var ReflectionORMClass
	 */
	private $reflectionClass;

	/**
	 * @var string
	 */
	private $loadSQL;

	/**
	 * @var string
	 */
	private $insertSQL;

	/**
	 * @var string
	 */
	private $deleteSQL;

	/**
	 * @param ReflectionORMClass $reflectionORM
	 */
	public function ORMNode(ReflectionORMClass $reflectionORM) {
		$this->reflectionClass = $reflectionORM;
	}

	/**
	 * @param DbDriver $driver
	 * @param object $entity
	 * @return void
	 */
	public function load(DbDriver $driver, $entity) {
		/** <TODO> Implement. */
	}

	/**
	 * @param DbDriver $driver
	 * @param object $entity
	 * @return void
	 */
	public function save(DbDriver $driver, $entity) {
		/** <TODO> Implement. */
	}

	/**
	 * @param DbDriver $driver
	 * @param object $entity
	 * @return void
	 */
	public function delete(DbDriver $driver, $entity) {
		/** <TODO> Implement. */
	}
}

?>
