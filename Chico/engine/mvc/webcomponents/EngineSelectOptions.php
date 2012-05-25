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
 * File: EngineSelectOptions.php
 **/


/** Componente de geracao de colunas nas listagens
 * @author Silas R. N. Junior
 */
class EngineSelectOptions {

	/** Nome da tag
	 * @var string
	 */
	const TAG_NAME = "selectOptions";

	/** Processa o modelo e o DOM
	 * @param DOMElement $selectElement   Elemento select resultante
	 * @param DOMElement $optElement   DOMElement com a tag das opcoes
	 * @param BaseModel $model   Objeto do Modelo referente a view
	 * @param mixed $selected   Valor selecionado na combo
	 * @return void
	 */
	public static function parse(DOMElement $selectElement, DOMElement $optElement, BaseModel $model, $selected) {
		$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
		preg_match_all($pattern,$optElement->getAttribute('values'),$valueArr);
		if (isset($valueArr['path'][0])) {
			$col = EntityUtils::getByPath($model,$valueArr['path'][0]);
		}
		$pattern = "/^(\\\$\{)(?P<method>[\w\d\.]+)(\})$/";
		preg_match_all($pattern,$optElement->getAttribute('values'),$valueArr);
		if (isset($valueArr['method'][0])) {
			$col = $model->{$valueArr['method'][0]}();
		}
		if (isset($col)) {
			$i = 0;
			foreach($col->toArray() as $item) {
				$opt = new DOMElement('option');
				$selectElement->appendChild($opt);
				if (strlen(trim($optElement->getAttribute('valueProperty'))) > 0 ) {
					$opt->setAttribute('value',EntityUtils::getByPath($item,$optElement->getAttribute('textProperty')));
				} else {
					$opt->setAttribute('value',$i++);
				}
				$opt->appendChild(new DOMText(EntityUtils::getByPath($item,$optElement->getAttribute('textProperty'))));
				if (is_object($selected)) {
					$rsel = new ReflectionClass($selected);
					$ritm = new ReflectionClass($item);

					//************************************************** inicio da modificacao ************************
					if ($selected instanceof Enumeration)
					$getterSelected = "getValue";
					else
					$getterSelected = EntityUtils::getGetter(EntityUtils::getIdPropertyName($rsel->getName()));

					if ($selected->{$getterSelected}() ==
					$item->{EntityUtils::getGetter(EntityUtils::getIdPropertyName($ritm->getName()))}()) {
						$opt->setAttribute('selected','selected');
						//**************************************************** fim da modificacao *************************
						//$selectElement->setAttribute('selectedIndex',$i - 1);
					}
				} else {
					if ($selected == $item) {
						$opt->setAttribute('selected','selected');
						//$selectElement->setAttribute('selectedIndex',$i - 1);
					}
				}
			}
		}
	}
}

?>