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
 * File: SizeAnnotation.php
 **/


/** Verifica se o array, lista, etc. se encontra entro minimo e o maximo
 * @author Silas R. N. Junior
 */
class SizeAnnotation extends Annotation {

	/** Quantidade minima de items
	 * @var int
	 */
	private $min;

	/** Quantidade maxima de items
	 * @var int
	 */
	private $max;

	/** Retorna a quantidade minima de items
	 * @return int
	 */
	public function getMin() {
		return $this->min;
	}

	/** Define a quantidade minima de items
	 * @param int $newMin   Quantidade minima de items
	 * @return void
	 */
	public function setMin($newMin) {
		$this->min = $newMin;
	}

	/** Retorna a quantidade maxima de items
	 * @return int
	 */
	public function getMax() {
		return $this->max;
	}

	/** Define a quantidade maxima de items
	 * @param int $newMax   Quantidade maxima de items
	 * @return void
	 */
	public function setMax($newMax) {
		$this->max = $newMax;
	}
}

?>