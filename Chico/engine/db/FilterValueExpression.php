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
 * File: FilterValueExpression.php
 **/

import('engine.db.IFilterCondition');

/** Expressao para filtro relacionando propriedade e valor
 * @author Silas R. N. Junior
 */
class FilterValueExpression implements IFilterCondition {

	/**
	 * @var string
	 */
	protected $property;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $operand;

	/**
	 * @param string $property   Nome da propriedade
	 * @param mixed $value   Valor da propriedade
	 * @param string $op   Operando da expressao
	 */
	public function FilterValueExpression($property, $value, $op) {
		$this->property = $property;
		$this->value = $value;
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
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param string $newValue
	 * @return void
	 */
	public function setValue($newValue) {
		$this->value = $newValue;
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
		if (is_null(DAOFactory::getDAO()->getDriver())) {
			throw new Exception("O DAO nao foi configurado. Nao e possivel executar operacoes com EntityFilter");
		}
		$splitPath = explode(".",$this->getProperty());
		if (count($splitPath) > 1 ) {
			$aliases = $entityFilter->getAliases();
			$class = $aliases[$splitPath[0]]['type'];
			$property = $splitPath[1];
			$alias = $splitPath[0];

		} else {
			
			$property = $this->getProperty();
			
			//Heranca
			if ($entityFilter->getClass()->getParentClass()) {
				$current = $entityFilter->getClass();
				while ($current) {
					/*
					 * [HACK] hasProperty nao funciona da forma desejada.
					 */
					$props = $current->getORMProperties();
					$has = false;
					foreach ($props as $p) {
						if ($p->name == $this->getProperty()) {
							$class = $current->getName();
							$alias = $entityFilter->getAlias().$current->getTableName();
							break 2;
						}
					}
					$current = $current->getParentORMClass();
				}
			} else {
				$class = $entityFilter->getClass()->getName();
				$alias = $entityFilter->getAlias();
			}
		}
			
		if (interface_exists($class)) {
			if (empty($aliases))
			throw new LogicException("Alias nao criado para a propriedade ".$property.".");
			//******** HACK - trabalhando com interfaces ********
			// TODO - Modificar isso aqui, está considerando somente uma nomenclatura onde a primeira letra pode ser desprezada
			$class = substr($class,1);
			//******** HACK - trabalhando com interfaces ********
		}
		$value = $this->getValue();
		if (isset($value) && is_object($value) && is_subclass_of($value,"Enumeration")) {
			$value = $value->ordinal();
		}
		//TODO - Tratar heranca
		$refrProperty = EntityManager::getReflectionData($class)->getORMProperty($property);
		return $alias.".".$entityFilter->getDAO()->getDriver()->formatField($refrProperty->getColumnName())
			.$this->getOperand()
			.$entityFilter->getDAO()->getDriver()->formatValue(
				$refrProperty->getType(),
				$value
			);
	}
}

?>