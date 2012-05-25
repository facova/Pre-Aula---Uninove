<?php
/**
 * Engine PHP Application Framework
 * http://seelaz.com.br
 *
 * Copyright (C) 2006-2010 Silas "Seelaz" Junior <seelaz@gmail.com>
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
 * File: VersionAnnotation.php
 **/


/** Anotacao de Versao. Define a coluna a ser utilizada para Optimistic Locking.
 * @author Silas R. N. Junior
 */
class VersioningAnnotation extends Annotation {

	/** Nome da coluna para versionamento
	 * @var string
	 */
	private $column = "version";

	/** Retorna o nome da coluna para versionamento
	 * @return string
	 */
	public function getColumn() {
		return $this->column;
	}

	/** Define o nome da coluna para versionamento
	 * @param string $newColumn
	 * @return void
	 */
	public function setColumn($newColumn) {
		$this->column = $newColumn;
	}
}

?>