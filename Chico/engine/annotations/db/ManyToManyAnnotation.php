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
 * @package annotations
 * @subpackage db
 * File: ManyToManyAnnotation.php
 **/

import('engine.annotations.db.AbstractRelationshipAnnotation');

/** Armazena dados sobre um relacionamento N:M entre entidades
 * @author Silas R. N. Junior
 */
class ManyToManyAnnotation extends AbstractRelationshipAnnotation {

	/** Coluna de uma col correspondente a outra entidade da relacao
	 * @var string
	 */
	private $inverseJoinColumns;

	/**
	 */
	public function ManyToManyAnnotation() {
		parent::AbstractRelationshipAnnotation();
	}

	/** Retorna a coluna de uma col correspondente a outra entidade da relacao
	 * @return string
	 */
	public function getInverseJoinColumns() {
		return $this->inverseJoinColumns;
	}

	/** Define a coluna de uma col correspondente a outra entidade da relacao
	 * @param string $newInverseJoinColumn   Nome da coluna
	 * @return void
	 */
	public function setInverseJoinColumns($newInverseJoinColumn) {
		$this->inverseJoinColumns = $newInverseJoinColumn;
	}

	/** Tipo da Associacao
	 * @return string
	 */
	public function getType() {
		return "ManyToMany";
	}
}

?>