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
 * File: EngineOutputText.php
 **/

import('engine.mvc.webcomponents.IEngineWebComponent');

/** Imprime o resultado da chamada de um metodo ou da propriedade referenciada no modelo
 * @author Silas R. N. Junior
 */
class EngineOutputText implements IEngineWebComponent {

	/** Nome da tag
	 * @var string
	 */
	const TAG_NAME = "outputText";

	/** Processa o modelo e o DOM
	 * @param DOMElement $e   DOMNodeList contendo as colunas da listagem
	 * @param mixed $value
	 * @param DOMElement $appendTo
	 * @return void
	 */
	public static function parse(DOMElement $e, $value,DOMElement $appendTo = null) {
		$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
		if (is_object($value)) {

			$pattern = "/(\#\{)(?P<path>[\w\d\.]+)(\})/";
			preg_match_all($pattern, $e->getAttribute('value'), $valueArr);
			if (isset($valueArr['path'][0])) {
				$labelIn = $e->getAttribute('value');
				foreach ($valueArr['path'] as $item) {
					$labelIn = @preg_replace($pattern, EntityUtils::getByPath($value,$item), $labelIn, $valueArr);
				}
				$labelText = $labelIn;
			} else {
				$labelText = $e->getAttribute('value');
			}
			$result = $labelText;
				
		} else {
			$result = $value;
		}
		if (@is_array($result)) {
			$parent = $e->parentNode;
			for ($i = 0; $i < count($result); $i++) {
				if (strlen(trim($e->getAttribute('enumClass'))) > 0) {
					$enum = trim($e->getAttribute('enumClass'));
					$result[$i] = new $enum($result[$i]);
				}
				if (isset($appendTo)) {
					$parent->appendChild(new DOMText($result[$i]));
					if ($i < count($result)) {
						$parent->appendChild(new DOMElement('br'));
					}
				} else {
					$appendTo = $e->parentNode->replaceChild(new DOMText($result[$i]),$e);
					if ($i < count($result)) {
						$parent->appendChild(new DOMElement('br'));
					}
				}
			}
		} else {
			if (strlen(trim($e->getAttribute('enumClass'))) > 0) {
				$enum = trim($e->getAttribute('enumClass'));
				$result = new $enum($result);
			}
			if (isset($appendTo)) {
				@$appendTo->appendChild(new DOMText($result));
			} else {
				$e->parentNode->replaceChild(new DOMText($result),$e);
			}
		}
	}
}

?>