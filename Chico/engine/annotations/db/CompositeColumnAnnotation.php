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
 * @package annotations
 * @subpackage db
 * File: CompositeColumnAnnotation.php
 **/


/** Define as colunas nas quais a propriedade esta mapeada
 * @author Silas R. N. Junior
 */
class CompositeColumnAnnotation extends Annotation {

	/** Colunas do mapeamento
	 * @var array
	 */
	private $columns;

	/**
	 * @param array $columns
	 */
	public function CompositeColumnAnnotation() {
	}

	/** Retorna as colunas da anotacao
	 * @return array
	 */
	public function getColumns() {
		return $this->columns;
	}

	/** Define as Colunas da anotacao
	 * @param array $newColumns
	 * @return void
	 */
	public function setColumns($newColumns) {
		$this->columns = $newColumns;
	}
}

?>
