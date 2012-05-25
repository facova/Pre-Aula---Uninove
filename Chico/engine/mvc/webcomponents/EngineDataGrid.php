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
 * File: EngineDataGrid.php
 **/

import('engine.mvc.webcomponents.IEngineWebComponent');
import('engine.mvc.webcomponents.EngineColumn');

/** Gerador de listas [tabelas]
 * @author Silas R. N. Junior
 */
class EngineDataGrid implements IEngineWebComponent {

	/** Nome da tag
	 * @var string
	 */
	const TAG_NAME = "dataGrid";

	/** Processa o modelo e o DOM
	 * @param DOMElement $e   DOMNodeList contendo as colunas da listagem
	 * @param BaseModel $model   Objeto do Modelo referente a view
	 * @return void
	 */
	public static function parse(DOMElement $e, BaseModel $model) {
		$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
		preg_match_all($pattern,$e->getAttribute('values'),$valueArr);
		if (isset($valueArr['path'][0])) {
			$objCollection = EntityUtils::getByPath($model,$valueArr['path'][0]);
		}
		$pattern = "/^(\\\$\{)(?P<method>[\w\d\.]+)(\})$/";
		preg_match_all($pattern,$e->getAttribute('values'),$valueArr);
		if (isset($valueArr['method'][0])) {
			$objCollection = $model->{$valueArr['method'][0]}();
		}
		$table = new DOMElement('table');
		$e->parentNode->replaceChild($table,$e);
		$thead = new DOMElement('thead');
		$table->appendChild($thead);
		$tbody = new DOMElement('tbody');
		$table->appendChild($tbody);
		$tfoot = new DOMElement('tfoot');
		$table->appendChild($tfoot);
		$colNodeList = $e->childNodes;
		$table->setAttribute('id',$e->getAttribute('id'));
		$table->setAttribute('name',$e->getAttribute('id'));
		$table->setAttribute('class',$e->getAttribute('class'));
		$table->setAttribute('width',$e->getAttribute('width'));
		$table->setAttribute('height',$e->getAttribute('height'));
		$table->setAttribute('border',$e->getAttribute('border'));
		$table->setAttribute('cellpadding',$e->getAttribute('cellpadding'));
		$table->setAttribute('cellspacing',$e->getAttribute('cellspacing'));
		if (isset($objCollection) && (($objCollection instanceof Collection)||($objCollection instanceof ArrayList))) {
			EngineColumn::parse($table,$colNodeList,$objCollection,$model);
		}
	}
}

?>