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


/** Componente de geracao de colunas nas listagens
 * @author Silas R. N. Junior
 */
class EngineColumn {

	/** Nome da tag
	 * @var string
	 */
	const TAG_NAME = "column";

	/** Procesa o registro da listagem
	 * @param DOMElement $tableElement   DOMElement referente a tabela da listagem
	 * @param DOMNodeList $colElementList   Listagem de colunas
	 * @param ArrayList $colObj   Objeto da entidade a ser listada
	 * @return void
	 */
	public static function parse(DOMElement $tableElement, DOMNodeList $colElementList,ArrayList $colObj,$model) {
		foreach ($colElementList as $column) {
			foreach ($column->childNodes as $colParameter) {
				if ($colParameter->localName == "columnHeader") {
					if ($tableElement->getElementsByTagName('thead')->item(0)->childNodes->length == 0) {
						$tr = new DOMElement('tr');
						$tableElement->getElementsByTagName('thead')->item(0)->appendChild($tr);
					} else {
						$tr = $tableElement->getElementsByTagName('thead')->item(0)->childNodes->item(0);
					}
					$th = new DOMElement('th');
					$tr->appendChild($th);
					$th->setAttribute('width',$column->getAttribute('width'));
					$th->setAttribute('class',$tableElement->getAttribute('headerClass'));

					$th->appendChild(new DOMText($colParameter->getAttribute('value')));
				}
			}
		}
		$j = 0;
		foreach ($colObj->toArray() as $listItem) {
			$row = new DOMElement('tr');
			$tableElement->getElementsByTagName('tbody')->item(0)->appendChild($row);
			$row->setAttribute('class',$tableElement->getAttribute('rowClass'));

			foreach ($colElementList as $column) {
				 
				$col = new DOMElement('td');
				$row->appendChild($col);
				$col->setAttribute('class',$tableElement->getAttribute('columnClass'));
				$col->setAttribute('width',$column->getAttribute('width'));
				 
				$colElement = $column;
				 
				foreach ($colElement->childNodes as $colParameter) {
					if ($colParameter instanceof DOMElement) {
						 
						switch ($colParameter->localName) {
							/*
							 case "columnHeader":
							 if (!isset($done)) {
							 if ($tableElement->getElementsByTagName('thead')->item(0)->childNodes->length == 0) {
							 $tr = new DOMElement('tr');
							 $tableElement->getElementsByTagName('thead')->item(0)->appendChild($tr);
							 } else {
							 $tr = $tableElement->getElementsByTagName('thead')->item(0)->childNodes->item(0);
							 }
							 $th = new DOMElement('th');
							 $tr->appendChild($th);
							 $th->appendChild(new DOMText($colParameter->getAttribute('value')));
							 }
							 break;
							 */
							case "columnValue":
								foreach ($colParameter->childNodes as $columnValueElement) {
									if ($columnValueElement instanceof DOMElement) {
										switch ($columnValueElement->localName) {
											case "actionField":
												//$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
												$pattern = "/(\#\{)(?P<path>[\w\d\.]+)(\})/";
												preg_match_all($pattern,$columnValueElement->getAttribute('label'),$valueArr);
												/*
												 if (isset($valueArr['path'][0])) {
												 $labelText = EntityUtils::getByPath($listItem,$valueArr['path'][0]);
												 } else {
												 $labelText = $columnValueElement->getAttribute('label');
												 }
												 */
												if (isset($valueArr['path'][0])) {
													$labelIn = $columnValueElement->getAttribute('label');
													//var_dump($labelIn);
													//$labelIn = preg_replace($pattern, EntityUtils::getByPath($listItem,$item), $columnValueElement->getAttribute('label'),$valueArr);
													foreach ($valueArr['path'] as $item) {
														//$labelText .= EntityUtils::getByPath($listItem,$item);
														$labelIn = preg_replace($pattern, EntityUtils::getByPath($listItem,$item), $labelIn, $valueArr);
														//echo $labelIn."</br>";
													}
													$labelText = $labelIn;
												} else {
													//$labelText = $columnValueElement->getAttribute('label');
													//echo "here<br/>";
													//preg_replace($pattern, $columnValueElement->getAttribute('label'), $columnValueElement->getAttribute('label'),$valueArr);
													$labelText = $columnValueElement->getAttribute('label');
												}
												$action = new DOMElement('input');
												$col->appendChild($action);
												$action->setAttribute('type','submit');
												$action->setAttribute('name','actionButton');
												$action->setAttribute('value',$labelText);
												$labelText = "";
												if ($columnValueElement->getAttribute('class')) $action->setAttribute('class',$columnValueElement->getAttribute('class'));
												if (strlen(trim($columnValueElement->getAttribute('value')))) {
													$pattern = "/^(\#\{)(?P<value>[\w\d\.]+)(\})$/";
													preg_match_all($pattern,$columnValueElement->getAttribute('value'),$valueArr);
													if (isset($valueArr['value'][0])) {
														$paramVal = EntityUtils::getByPath($listItem,$valueArr['value'][0]);
													} else {
														$paramVal = $columnValueElement->getAttribute('value');
													}
												} else {
													$paramVal = $j;
												}
												$action->setAttribute('onclick',"this.form.action = '".$columnValueElement->getAttribute('action')."/".$paramVal."'; this.form.submit(); return false;");
													
												break;
											case "outputText":
												EngineOutputText::parse($columnValueElement,$listItem,$col);
												break;
											case "inputBooleanCheckbox":
												$action = new DOMElement('input');
												$col->appendChild($action);
												$action->setAttribute('type','checkbox');
												$action->setAttribute('id',$columnValueElement->getAttribute('id')."-".$j);
												$action->setAttribute('name',$columnValueElement->getAttribute('id')."[]");
												$action->setAttribute('value',$j);
												if (EntityUtils::getValueByPath($model,$columnValueElement->getAttribute('value')) == true) {
													$action->setAttribute('checked','checked');
												}
												break;
											case "actionImage":
												$pattern = "/(\#\{)(?P<path>[\w\d\.]+)(\})/";
												preg_match_all($pattern, $columnValueElement->getAttribute('label'), $valueArr);
												if (isset($valueArr['path'][0])) {
													$labelIn = $columnValueElement->getAttribute('label');
													foreach ($valueArr['path'] as $item)
													$labelIn = preg_replace($pattern, EntityUtils::getByPath($listItem,$item), $labelIn, $valueArr);
													$labelText = $labelIn;
												} else {
													$labelText = $columnValueElement->getAttribute('label');
												}
													
												$link = new DOMElement('a');
												$col->appendChild($link);
												//if ($columnValueElement->hasAttribute('href'))
												//		$link->setAttribute('href', $columnValueElement->getAttribute('href'));
												if ($columnValueElement->hasAttribute('title'))
												$link->setAttribute('title', $columnValueElement->getAttribute('title'));
													
												$image = new DOMElement('img');
												$link->appendChild($image);
												if ($columnValueElement->hasAttribute('src'))
												$image->setAttribute('src', $columnValueElement->getAttribute('src'));
												if ($columnValueElement->hasAttribute('width'))
												$image->setAttribute('width', $columnValueElement->getAttribute('width'));
												if ($columnValueElement->hasAttribute('height'))
												$image->setAttribute('height', $columnValueElement->getAttribute('height'));
												if ($columnValueElement->hasAttribute('alt'))
												$image->setAttribute('alt', $columnValueElement->getAttribute('alt'));
												if ($columnValueElement->hasAttribute('class'))
												$image->setAttribute('class', $columnValueElement->getAttribute('class'));
													
												$paramVal = $j;
												$link->setAttribute('href', $columnValueElement->getAttribute('action')."/".$paramVal);
												//$action->setAttribute('onclick',"this.form.action = '".$columnValueElement->getAttribute('action')."/".$paramVal."'; this.form.submit(); return false;");
												break;
												//default:
												//	$col->appendChild($columnValueElement);
										}
									}
								}
								break;
						}
					} else {
						//$row->appendChild($colElement);
					}
				}
			}
			$done = true;
			$j++;
		}
	}
}

?>