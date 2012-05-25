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
 * File: EngineSelectOne.php
 **/

import('engine.mvc.webcomponents.IEngineWebComponent');
import('engine.mvc.webcomponents.EngineSelectOptions');

/** Gerador de comboboxes
 * @author Silas R. N. Junior
 */
class EngineSelectOne implements IEngineWebComponent {

	/** Nome da tag
	 * @var string
	 */
	const TAG_NAME = "selectOne";

	/** Processa o modelo e o DOM
	 * @param DOMElement $e   DOMNodeList contendo as colunas da listagem
	 * @param BaseModel $model   Objeto do Modelo referente a view
	 * @return void
	 */
	public static function parse(DOMElement $e, BaseModel $model) {
		$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
		preg_match_all($pattern,$e->getAttribute('value'),$valueArr);
		if (isset($valueArr['path'][0])) {
			$selected = EntityUtils::getByPath($model,$valueArr['path'][0]);
		}
		$select = new DOMElement('select');
		$optNodeList = $e->childNodes;
		$e->parentNode->replaceChild($select,$e);
		$select->setAttribute('id',$e->getAttribute('id'));
		$select->setAttribute('name',$e->getAttribute('name'));
		$select->setAttribute('class',$e->getAttribute('class'));
		$select->setAttribute('onchange',$e->getAttribute('onchange'));
		if($optNodeList->length > 0) {
			//for ($i = $optNodeList->length; --$i >= 0; ) {
			for ($i = 0; $i < $optNodeList->length; $i++ ) {
				$opt = $optNodeList->item($i);
				if ($opt instanceof DOMElement) {
					if ($opt->localName == "option") {
						$opt = $select->appendChild($opt->cloneNode(1));
						$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
						preg_match_all($pattern,$e->getAttribute('values'),$valueArr);
						if (isset($valueArr['path'][0])) {
							$optValue = EntityUtils::getByPath($model,$valueArr['path'][0]);
						}
						$pattern = "/^(\\\$\{)(?P<method>[\w\d\.]+)(\})$/";
						preg_match_all($pattern,$e->getAttribute('values'),$valueArr);
						if (isset($valueArr['method'][0])) {
							$optValue = $model->{$valueArr['method'][0]}();
						}
						if (!isset($optValue)) {
							$optValue =  $opt->getAttribute('value');
						}

						if (is_object($selected)) {
							if (@spl_object_hash($selected) == @spl_object_hash($optValue)) {
								//$select->setAttribute('selectedIndex',$opt->getAttribute('index'));
								$opt->setAttribute('selected','selected');
							}
						} else {
							if ($selected == $optValue) {
								//$select->setAttribute('selectedIndex',$opt->getAttribute('index'));
								$opt->setAttribute('selected','selected');
							}
						}
						unset($optValue);
					}
				}
			}
		}
		$options = $e->getElementsByTagName(EngineSelectOptions::TAG_NAME);
		if ($options->length > 0) {
			EngineSelectOptions::parse($select,$options->item(0),$model,$selected);
		}
	}
}

?>