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
 * File: CachedObjectContainer.php
 **/

import("engine.db.IEntityContainer");

/** Conteiner de objetos no cache guarda diversas informacoes sobre o estado do objeto
 * @author Silas R. N. Junior
 */
class CachedObjectContainer implements IEntityContainer {

	/** Instancia do objeto em cache
	 * @var object
	 */
	private $subject;

	/** String da serializacao do estado do objeto
	 * @var string
	 */
	private $lastState;
	
	/** Versao do objeto na base de dados
	 * @var int
	 */
	private $version;

	/**
	 * @param object $entity
	 */
	public function CachedObjectContainer(&$entity, $version = null) {
		//$this->lastState = md5(serialize($entity));
		$this->lastState = EntityManager::makeStateString($entity);
		$this->subject = &$entity;
		$this->version = $version;
	}

	/** Verifica se o objeto sofreu alteracoes desde o ultimo refresh
	 * @return boolean
	 */
	public function isDirty() {
		return $this->lastState == EntityManager::makeStateString($this->subject) ? false : true;
	}

	/** Atualiza o estado armazenado do objeto
	 * @return void
	 */
	public function refreshState() {
		$this->lastState = EntityManager::makeStateString($this->subject);
	}
	
	/** Retorna a versao do objeto armazenado
	 * @return int Versao do objeto
	 */
	public function getVersion() {
		return $this->version;
	}
	
	/** Define a versao do objeto armazenado
	 * @param int $newVersion
	 * @return void
	 */
	public function setVersion($newVersion) {
		return $this->version = $newVersion;
	}

	/** Retorna o objeto armazenado
	 * @return object
	 */
	public function &getSubject() {
		return $this->subject;
	}
}

?>
