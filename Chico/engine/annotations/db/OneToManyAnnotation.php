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
 * File: OneToManyAnnotation.php
 **/

import('engine.annotations.db.AbstractRelationshipAnnotation');

/** Armazena dados sobre um relacionamento 1:N entre entidades
 * @author Silas R. N. Junior
 */
class OneToManyAnnotation extends AbstractRelationshipAnnotation {

	/** Coluna de uma col correspondente a outra entidade da relacao
	 * @var string
	 */
	private $inverseJoinColumn;

	/** Determina se o framework deve deletar a entidade no momento da remocao da colecao.
	 * @var boolean
	 */
	private $deleteOrphan = false;

	/**
	 */
	public function OneToManyAnnotation() {
		parent::AbstractRelationshipAnnotation();
	}

	/** Retorna a coluna de uma col correspondente a outra entidade da relacao
	 * @return string
	 */
	public function getInverseJoinColumn() {
		return $this->inverseJoinColumn;
	}

	/** Define a coluna de uma col correspondente a outra entidade da relacao
	 * @param string $newInverseJoinColumn   Nome da coluna
	 * @return void
	 */
	public function setInverseJoinColumn($newInverseJoinColumn) {
		$this->inverseJoinColumn = $newInverseJoinColumn;
	}

	/** Tipo da Associacao
	 * @return string
	 */
	public function getType() {
		return "OneToMany";
	}

	/** Define se o framework deve excluir as entidades ao remover da colecao
	 * @param boolean $newDeleteOrphan
	 * @return void
	 */
	public function setDeleteOrphan($newDeleteOrphan) {
		$this->deleteOrphan = $newDeleteOrphan;
	}

	/** Verifica se o framework deve excluir as entidades ao remover da colecao
	 * @return boolean
	 */
	public function isDeleteOrphan() {
		return $this->deleteOrphan;
	}
}

?>