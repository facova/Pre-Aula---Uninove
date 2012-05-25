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
 * File: FilterParameter.php
 **/

import('engine.db.IFilterCondition');
import('engine.db.FilterCondition');
import('engine.db.FilterValueExpression');
import('engine.db.FilterLikeValueExpression');
import('engine.db.FilterPropertyExpression');

/** Objeto de parametro para filtro
 * @author Silas R. N. Junior
 */
class FilterParameter {

	/** Adiciona uma constraint de "igual a" a propriedade fornecida
	 * @param string $property   Nome da propriedade
	 * @param mixed $value   Valor da expressao
	 * @return FilterValueExpression
	 */
	public static function eq($property, $value) {
		return new FilterValueExpression($property,$value," = ");
	}

	/** Adiciona uma constraint de "diferente de" a propriedade fornecida
	 * @param string $property   Nome da propriedade
	 * @param mixed $value   Valor da expressao
	 * @return FilterValueExpression
	 */
	public static function ne($property, $value) {
		return new FilterValueExpression($property,$value," <> ");
	}

	/** Adiciona uma constraint de "menor que" a propriedade fornecida
	 * @param string $property   Nome da propriedade
	 * @param mixed $value   Valor da expressao
	 * @return FilterValueExpression
	 */
	public static function lt($property, $value) {
		return new FilterValueExpression($property,$value," < ");
	}

	/** Adiciona uma constraint de "maior que" a propriedade fornecida
	 * @param string $property   Nome da propriedade
	 * @param mixed $value   Valor da expressao
	 * @return FilterValueExpression
	 */
	public static function gt($property, $value) {
		return new FilterValueExpression($property,$value," > ");
	}

	/** Adiciona uma constraint de "menor ou igual a" a propriedade fornecida
	 * @param string $property   Nome da propriedade
	 * @param mixed $value   Valor da expressao
	 * @return FilterValueExpression
	 */
	public static function le($property, $value) {
		return new FilterValueExpression($property,$value," <= ");
	}

	/** Adiciona uma constraint de "maior ou igual a" a propriedade fornecida
	 * @param string $property   Nome da propriedade
	 * @param mixed $value   Valor da expressao
	 * @return FilterValueExpression
	 */
	public static function ge($property, $value) {
		return new FilterValueExpression($property,$value," >= ");
	}

	/** Adiciona uma constraint de "semelhante a" a propriedade fornecida [o caractere curinga '%' pode ser utilizado para determinar o inicio, termino ou um trecho do dado pesquisado]
	 * @param string $property   Nome da propriedade
	 * @param mixed $value   Valor da expressao
	 * @return FilterValueExpression
	 */
	public static function like($property, $value) {
		return new FilterLikeValueExpression($property,$value," like ");
	}

	/** Adiciona uma constraint de "existe no conjunto"
	 * @param string $property   Nome da propriedade
	 * @return FilterValueExpression
	 */
	public static function isIn($property, $valuesArray) {
		/**
		 * @TODO Implementar a operacao IN e utilizacao de arrays no operando do lado direito
		 */
		//return new FilterValueExpression($property, $valuesArray," IN ");
	}

	/** Adiciona uma constraint de "e nulo"
	 * @param string $property   Nome da propriedade
	 * @return FilterValueExpression
	 */
	public static function isNull($property) {
		return new FilterValueExpression($property,"NULL"," IS ");
	}

	/** Adiciona uma constraint de "nao e nulo"
	 * @param string $property   Nome da propriedade
	 * @return FilterValueExpression
	 */
	public static function isNotNull($property) {
		return new FilterValueExpression($property,"NULL"," IS NOT ");
	}

	/** Adiciona uma constraint de "igual a" entre duas propriedades
	 * @param string $property   Nome da propriedade
	 * @param string $otherProperty   Nome da outra propriedade
	 * @return FilterPropertyExpression
	 */
	public static function eqProperty($property, $otherProperty) {
		return new FilterPropertyExpression($property,$otherProperty," = ");
	}

	/** Adiciona uma constraint de "diferente de" entre duas propriedades
	 * @param string $property   Nome da propriedade
	 * @param string $otherProperty   Nome da outra propriedade
	 * @return FilterPropertyExpression
	 */
	public static function neProperty($property, $otherProperty) {
		return new FilterPropertyExpression($property,$otherProperty," <> ");
	}

	/** Adiciona uma constraint de "menor que" entre duas propriedades
	 * @param string $property   Nome da propriedade
	 * @param string $otherProperty   Nome da outra propriedade
	 * @return FilterPropertyExpression
	 */
	public static function ltProperty($property, $otherProperty) {
		return new FilterPropertyExpression($property,$otherProperty," < ");
	}

	/** Adiciona uma constraint de "maior que" entre duas propriedades
	 * @param string $property   Nome da propriedade
	 * @param string $otherProperty   Nome da outra propriedade
	 * @return FilterPropertyExpression
	 */
	public static function gtProperty($property, $otherProperty) {
		return new FilterPropertyExpression($property,$otherProperty," > ");
	}

	/** Adiciona uma constraint de "menor ou igual a" entre duas propriedades
	 * @param string $property   Nome da propriedade
	 * @param string $otherProperty   Nome da outra propriedade
	 * @return FilterPropertyExpression
	 */
	public static function leProperty($property, $otherProperty) {
		return new FilterPropertyExpression($property,$otherProperty," <= ");
	}

	/** Adiciona uma constraint de "maior ou igual a" entre duas propriedades
	 * @param string $property   Nome da propriedade
	 * @param string $otherProperty   Nome da outra propriedade
	 * @return FilterPropertyExpression
	 */
	public static function geProperty($property, $otherProperty) {
		return new FilterPropertyExpression($property,$otherProperty," >= ");
	}

	/** Cria uma operacao AND entre duas condicoes
	 * @param IFilterCondition $condition
	 * @param IFilterCondition $otherCondition
	 * @return FilterCondition
	 */
	public static function andConditions(IFilterCondition $condition = null, IFilterCondition $otherCondition = null) {
		$cond = new FilterCondition();
		$cond->add($condition);
		$cond->add($otherCondition);
		$cond->setOperand(" AND ");
		return $cond;
	}

	/** Cria uma operacao OR entre duas condicoes
	 * @param IFilterCondition $condition
	 * @param IFilterCondition $otherCondition
	 * @return FilterCondition
	 */
	public static function orConditions(IFilterCondition $condition = null, IFilterCondition $otherCondition = null) {
		$cond = new FilterCondition();
		if ($condition) $cond->add($condition);
		if ($otherCondition) $cond->add($otherCondition);
		$cond->setOperand(" OR ");
		return $cond;
	}
}

?>