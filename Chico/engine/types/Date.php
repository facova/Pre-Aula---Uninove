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
 * @subpackage types
 * File: Date.php
 **/

import('engine.types.Type');

/** Fornece funcoes para operacoes com datas
 * @author Silas R. N. Junior
 */
class Date extends Type {

	/** Data no formato timestamp
	 * @var string
	 */
	private $value;

	/**
	 */
	public function Date($value) {
		$this->value = $value;
	}

	/** Retorna a data do objeto formatada com a mascara fornecida
	 * @param string $s
	 * @return string
	 */
	public function formatByMask($s) {
		/** <TODO> Implement. */
	}

	/** Adiciona dias a data setada. Aceita parametros de valor negativo
	 * @param int $n   Numero de dias
	 * @return void
	 */
	public function addDays($n) {
		/** <TODO> Implement. */
	}

	/** Adiciona meses a data setada. Aceita parametros de valor negativo
	 * @param int $n   Numero de Meses
	 * @return void
	 */
	public function addMonths($n) {
		/** <TODO> Implement. */
	}

	/** Adiciona anos a data setada. Aceita parametros de valor negativo
	 * @param int $n   Numero de anos
	 * @return void
	 */
	public function addYears($n) {
		/** <TODO> Implement. */
	}

	/** retorna o dia de hoje [opcional: $mask utiliza a mascara fornecida para formatar a data]
	 * @param string $mask
	 * @return string
	 */
	public function today($mask = null) {
		/** <TODO> Implement. */
	}

	/** Retorna a data
	 * @return string
	 */
	public function getDate() {
		return $this->date;
	}

	/** Define uma data
	 * @param string $newDate   Data
	 * @return void
	 */
	public function setDate($newDate) {
		$this->value = $newDate;
	}

	/** Retornao valor corrente do tipo em string
	 * @return string
	 */
	public function __toString() {
		return (string)$this->value;
	}
}

?>