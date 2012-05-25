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

import('engine.db.FilterValueExpression');

/** Expressao para filtro relacionando propriedade e valor
 * @author Silas R. N. Junior
 */
class FilterLikeValueExpression extends FilterValueExpression {

	public function FilterLikeValueExpression($property, $value, $op) {
		$this->property = $property;
		$this->value = $value;
		$this->operand = $op;
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
			// TODO - Modificar isso aqui, está considerando somente uma nomenclatura inde a primeira letra pode ser desprezada
			$class = substr($class,1);
			//******** HACK - trabalhando com interfaces ********
		}
		$value = $this->getValue();
		if (isset($value) && is_object($value) && is_subclass_of($value,"Enumeration")) {
			$value = $value->ordinal();
		}
		//TODO - Tratar heranca
		$refrProperty = EntityManager::getReflectionData($class)->getORMProperty($property);
		return $entityFilter->getDAO()->getDriver()->toUpper($alias.".".$entityFilter->getDAO()->getDriver()->formatField($refrProperty->getColumnName()))
			.$this->getOperand()
			.$entityFilter->getDAO()->getDriver()->formatValue(
				$refrProperty->getType(),
				strtoupper($value)
			);
	}
}

?>