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
 * File: FilterGroupBy.php
 **/

import('engine.db.IFilterCondition');

/** Agrupamento de resultados
 * @author Silas R. N. Junior
 */
class FilterGroupBy implements IFilterCondition {

	/** Propriedade para agrupar os resultados
	 * @var string
	 */
	private $propertyName;

	/**
	 * @param string $propertyName   Nome da propriedade
	 */
	public function FilterGroupBy($propertyName) {
		$this->propertyName = $propertyName;
	}

	/** Define a propriedade para agrupamento dos resultados
	 * @param string $propertyName   Nome da propriedade
	 * @return void
	 */
	public static function groupProperty($propertyName) {
		return new FilterGroupBy($propertyName);
	}

	public function getPath() {
		return $this->propertyName;
	}

	/** Gera o codigo sql
	 * @param EntityFilter $entityFilter
	 * @return string
	 */
	public function toSql(EntityFilter $entityFilter) {
		$splitPath = explode(".",$this->propertyName);
		if (count($splitPath) > 1 ) {
			$aliases = $entityFilter->getAliases();
			$class = $aliases[$splitPath[0]]['type'];
			$property = $splitPath[1];
			$alias = $splitPath[0];
		} else {
			$class = $entityFilter->getClass()->getName();
			$property = $this->propertyName;
			$alias = $entityFilter->getAlias();
		}
		return $alias.".".$entityFilter->getDAO()->getDriver()->formatField(EntityUtils::getPropertyColumnName($class,$property));
	}
}

?>
