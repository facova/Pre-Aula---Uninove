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
 * File: FilterPropertyExpression.php
 **/

import('engine.db.IFilterCondition');

/** Expressao para filtro relacionando duas propriedades
 * @author Silas R. N. Junior
 */
class FilterPropertyExpression implements IFilterCondition {

	/**
	 * @var string
	 */
	private $property;

	/**
	 * @var string
	 */
	private $otherProperty;

	/**
	 * @var string
	 */
	private $operand;

	/**
	 * @param string $property   Nome da propriedade
	 * @param string $otherProperty   Nome da outra propriedade
	 * @param string $op   Operando da expressao
	 */
	public function FilterPropertyExpression($property, $otherProperty, $op) {
		$this->property = $property;
		$this->otherProperty = $otherProperty;
		$this->operand = $op;
	}

	/**
	 * @return string
	 */
	public function getProperty() {
		return $this->property;
	}

	/**
	 * @param string $newProperty
	 * @return void
	 */
	public function setProperty($newProperty) {
		$this->property = $newProperty;
	}

	/**
	 * @return string
	 */
	public function getOtherProperty() {
		return $this->otherProperty;
	}

	/**
	 * @param string $newOtherProperty
	 * @return void
	 */
	public function setOtherProperty($newOtherProperty) {
		$this->otherProperty = $newOtherProperty;
	}

	/**
	 * @return string
	 */
	public function getOperand() {
		return $this->operand;
	}

	/**
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
		$splitPath = explode(".",$this->getProperty());
		$aliases = $entityFilter->getAliases();
		if ( count($splitPath) > 1 ) {
			$propCol = EntityManager::getClassMeta($aliases[$splitPath[0]]['type'])->getPropertyMeta($splitPath[1])->getColumnMeta()->getName();
			$propAlias = $splitPath[0];
		} else {
			$propCol = EntityManager::getClassMeta($entityFilter->getClass()->getName())->getPropertyMeta($this->getProperty())->getColumnMeta()->getName();
			$propAlias = $entityFilter->getAlias();
		}
		 
		$oSplitPath = explode(".",$this->getOtherProperty());
		if ( count($oSplitPath) > 1 ) {
			$oPropCol = EntityManager::getClassMeta($aliases[$oSplitPath[0]]['type'])->getPropertyMeta($oSplitPath[1])->getColumnMeta()->getName();
			$oPropAlias = $oSplitPath[0];
		} else {
			$oPropCol = EntityManager::getClassMeta($entityFilter->getClass()->getName())->getPropertyMeta($this->getProperty())->getColumnMeta()->getName();
			$oPropAlias = $entityFilter->getAlias();
		}
		return $propAlias.".".$entityFilter->getDAO()->getDriver()->formatField($propCol).$this->getOperand().$oPropAlias.".".$entityFilter->getDAO()->getDriver()->formatField($oPropCol);
	}
}

?>