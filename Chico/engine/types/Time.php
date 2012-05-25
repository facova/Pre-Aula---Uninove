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
 * File: Time.php
 **/

import('engine.types.Type');

/** Fornce funcoes para operacoes com Tempo
 * @author Silas R. N. Junior
 */
class Time extends Type {

	/** Tempo no formato timestamp
	 * @var string
	 */
	private $value;

	/**
	 */
	public function Time($value) {
		$this->value = $value;
	}

	/** Retorna a hora atual
	 * @return string
	 */
	public function currentTime() {
		/** <TODO> Implement. */
	}

	/** Adiciona horas a hora setada. Aceita parametros de valor negativo
	 * @param int $n   Numero de Horas
	 * @return void
	 */
	public function addHours($n) {
		/** <TODO> Implement. */
	}

	/** Adiciona minutos a hora setada. Aceita parametros de valor negativo
	 * @param int $n   Numero de Minutos
	 * @return void
	 */
	public function addMinutes($n) {
		/** <TODO> Implement. */
	}

	/** Retorna a hora
	 * @return string
	 */
	public function getTime() {
		return $this->value;
	}

	/** Define a hora
	 * @param string $newTime   Hora
	 * @return void
	 */
	public function setTime($newTime) {
		$this->value = $newTime;
	}

	/** Retornao valor corrente do tipo em string
	 * @return string
	 */
	public function __toString() {
		return (string)$this->value;
	}
}

?>