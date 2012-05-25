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
 * @subpackage exceptions
 * File: ConstraintException.php
 **/

import('engine.exceptions.SQLException');

/**
 * @author Silas R. N. Junior
 */
class ConstraintException extends SQLException {

	const FOREIGN = "FOREIGN";
	const UNIQUE = "UNIQUE";
	const PRIMARY = "PRIMARY";

	/** Nome da constraint
	 * @var string
	 */
	private $constraint;

	/** Tipo da constraint
	 * @var string
	 */
	private $type;

	/**
	 * @return string
	 */
	public function getConstraint() {
		return $this->constraint;
	}

	/**
	 * @param string $newConstraint
	 * @return void
	 */
	public function setConstraint($newConstraint) {
		$this->constraint = $newConstraint;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $newType
	 * @return void
	 */
	public function setType($newType) {
		$this->type = $newType;
	}
}

?>