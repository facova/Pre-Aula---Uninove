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
 * @subpackage db
 * File: FilterCondition.php
 **/

import("engine.db.IFilterCondition");

/** Condicoes de uma busca
 * @author Silas R. N. Junior
 */
class FilterCondition implements IFilterCondition {

	/** Condicoes componentes desta condicao
	 * @var array
	 */
	private $conditions;

	/** Operando das operacoes entre as conditions do grupo
	 * @var string
	 */
	private $operand;

	/** Adiciona uma condicao ao grupo de condicoes
	 * @param IFilterCondition $condition
	 * @return FilterCondition
	 */
	public function add(IFilterCondition $condition) {
		$this->conditions[] = $condition;
		return $this;
	}

	/** Retorna o operando das operacoes deste grupo de condicoes
	 * @return string
	 */
	public function getOperand() {
		return $this->operand;
	}

	/** Define o operando das operacoes deste grupo de condicoes
	 * @param string $newOperand
	 * @return void
	 */
	public function setOperand($newOperand) {
		$this->operand = $newOperand;
	}

	/** Gera o codigo sql
	 * @param EntityFilter $entityFilter
	 * @return string
	 */
	public function toSql(EntityFilter $entityFilter) {
		$sql = array();
		foreach ($this->conditions as $condition) {
			$sql[] = $condition->toSql($entityFilter);
		}
		return "(".implode($this->operand,$sql).")";
	}
}

?>