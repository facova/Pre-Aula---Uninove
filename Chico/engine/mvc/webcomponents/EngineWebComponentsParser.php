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
 * File: EngineWebComponentsParser.php
 **/


/** Processa tags corresponendentes a componentes do Framework
 * @author Silas R. N. Junior
 */
class EngineWebComponentsParser {

	public static $ns = "http://seelaz.com.br/EngineML";

	/** Processa a taglib atualizando o DOM da View
	 * @param DOMElement $element
	 * @param BaseModel $model
	 * @return IEngineWebComponent
	 */
	public static function parse(BaseView $view) {
		$elements = $view->getDomDocument()
		->getElementsByTagNameNS(self::$ns,EngineIf::TAG_NAME);
		if ($elements->length > 0) {
			import('engine.mvc.webcomponents.EngineIf');
			for ($i = $elements->length; --$i >= 0; ) {
				$element = $elements->item($i);
				EngineIf::parse($element,$view->getModel());
			}
		}
		$elements = $view->getDomDocument()
		->getElementsByTagNameNS(self::$ns,EngineDataGrid::TAG_NAME);
		if ($elements->length > 0) {
			import('engine.mvc.webcomponents.EngineDataGrid');
			for ($i = $elements->length; --$i >= 0; ) {
				$element = $elements->item($i);
				EngineDataGrid::parse($element,$view->getModel());
			}
		}
		$elements = $view->getDomDocument()
		->getElementsByTagNameNS(self::$ns,EngineOutputText::TAG_NAME);
		if ($elements->length > 0) {
			import('engine.mvc.webcomponents.EngineOutputText');
			for ($i = $elements->length; --$i >= 0; ) {
				$element = $elements->item($i);
				EngineOutputText::parse($element,$view->getModel());
			}
		}
		$elements = $view->getDomDocument()
		->getElementsByTagNameNS(self::$ns,EngineSelectOne::TAG_NAME);
		if ($elements->length > 0) {
			import('engine.mvc.webcomponents.EngineSelectOne');
			for ($i = $elements->length; --$i >= 0; ) {
				$element = $elements->item($i);
				EngineSelectOne::parse($element,$view->getModel());
			}
		}
		$elements = $view->getDomDocument()
		->getElementsByTagNameNS(self::$ns,EngineSelectMany::TAG_NAME);
		if ($elements->length > 0) {
			import('engine.mvc.webcomponents.EngineSelectMany');
			for ($i = $elements->length; --$i >= 0; ) {
				$element = $elements->item($i);
				EngineSelectMany::parse($element,$view->getModel());
			}
		}
	}
}

?>