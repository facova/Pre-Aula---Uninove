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
 * File: IdAnnotation.php
 **/


/** Define se o campo é um indice.
 * @author Silas R. N. Junior
 */
class IdAnnotation extends Annotation {

	/**
	 * @var string
	 */
	private $strategy;

	/**
	 */
	public function IdAnnotation() {
		$this->strategy = GenerationType::AUTO;
	}

	/** Retona a estrategia de geracao de indice
	 * @return string
	 */
	public function getStrategy() {
		return $this->strategy;
	}

	/** Define a estrategia de geracao de indice
	 * @param string $newStrategy
	 * @return void
	 */
	public function setStrategy($newStrategy) {
		$this->strategy = $newStrategy;
	}
}

?>