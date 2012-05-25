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
 * @package mvc
 * @subpackage webcomponents
 * File: EngineColumn.php
 **/


/** Componente de geracao de condicionais
 * @author Silas R. N. Junior
 */
class EngineIf {

	/** Nome da tag
	 * @var string
	 */
	const TAG_NAME = "if";

	/** Processa o registro da listagem
	 * @param DOMElement $element
	 * @param mixed $value
	 * @param DOMElement $appendTo
	 * @return void
	 */
	public static function parse(DOMElement $element, $value, DOMElement $appendTo = null) {
		//value eh um model MVC
		if (is_object($value)) {
			$pattern = "/(\#\{)(?P<path>[\w\d\.]+)(\})/";
			preg_match_all($pattern, $element->getAttribute('assert'), $valueArr);
			if (isset($valueArr['path'][0])) {
				$isArray = true;
				$labelIn = $element->getAttribute('assert');
				foreach ($valueArr['path'] as $item) {
					$labelIn = @preg_replace($pattern, EntityUtils::getByPath($value,$item), $labelIn, $valueArr);
				}
				$labelText = $labelIn;
			} else {
				$labelText = $element->getAttribute('assert');
			}
			$result = $labelText;
		} else {
			$result = $value;
		}

		$partial = $result;

		//se a condicional fornecida for verdadeira, realizar output dos elementos internos
		if (eval("return (".$result.");")) {
			if ($element->hasChildNodes()) {
				$children = $element->childNodes;
				$element->parentNode->replaceChild($children->item(0),$element);
			}
		} else {
			$element->parentNode->replaceChild(new DOMText(""), $element);
		}
	}
}

?>