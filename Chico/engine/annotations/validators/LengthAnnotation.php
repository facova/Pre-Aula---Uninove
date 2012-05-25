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
 * @subpackage validators
 * File: LengthAnnotation.php
 **/


/** Verifica se o tamanho da string confere
 * @author Silas R. N. Junior
 */
class LengthAnnotation extends Annotation {

	/** Tamanho minimo da string
	 * @var int
	 */
	private $min;

	/** Tamanho maximo da string
	 * @var int
	 */
	private $max;

	/** Retorna o tamanho minimo da string
	 * @return int
	 */
	public function getMin() {
		return $this->min;
	}

	/** Define o tamanho minimo da string
	 * @param int $newMin   Tamanho minimo da string
	 * @return void
	 */
	public function setMin($newMin) {
		$this->min = $newMin;
	}

	/** Retorna o tamanho maximo da string
	 * @return int
	 */
	public function getMax() {
		return $this->max;
	}

	/** Define o tamanho maximo da string
	 * @param int $newMax   Tamanho maximo da string
	 * @return void
	 */
	public function setMax($newMax) {
		$this->max = $newMax;
	}
}

?>