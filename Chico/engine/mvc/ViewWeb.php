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
 * @subpackage mvc
 * File: ViewWeb.php
 **/

import('engine.db.EntityUtils');
import('engine.mvc.webcomponents.EngineWebComponentsParser');

/** View da aplicacao com output para WEB
 * @author Silas R. N. Junior
 */
class ViewWeb extends BaseView {

	/** Instancia DOMDocument
	 * @var DOMDocument
	 */
	private $domDocument;

	/** Caminho do template ao qual essa instancia se refere
	 * @var string
	 */
	private $path;

	/** Cache do documento DOM
	 * @var string
	 */
	private $domRawCache;

	/** Construtor da View
	 * @param string $viewName   String de pacote da view
	 */
	public function ViewWeb($viewName) {
		$this->loadDomDocument($viewName);
		$this->path = $viewName;
		$modelEl = $this->domDocument->getElementsByTagNameNS(EngineWebComponentsParser::$ns,"model");
		if ($modelEl->length == 1) {
			if ($modelEl->item(0)->getAttribute('name') == "none") {
				throw new Exception("O componente Modelo � obrigat�rio");
				$model = null;
			} else {
				$modelClass = $modelEl->item(0)->getAttribute('name').'Model';
				import(APP_NAME.'.model.'.$modelClass);
				$model = new $modelClass();
				$this->setModel($model);
				//***************************************** modificado inicio
				$tempModelName = $modelEl->item(0)->getAttribute('name');
				//***************************************** modificado fim
				$modelEl->item(0)->parentNode->removeChild($modelEl->item(0));
			}
		} else {
			$modelClass = $viewName.'Model';
			import(APP_NAME.'.model.'.$modelClass);
			$model = new $modelClass();
			$this->setModel($model);
		}
		$controllerEl = $this->domDocument->getElementsByTagNameNS(EngineWebComponentsParser::$ns,"controller");
		if ($controllerEl->length == 1) {
			if ($controllerEl->item(0)->getAttribute('name') == "none") {
				$controller = null;
			} else {
				//***************************************** modificado inicio
				//$controllerClass = $modelEl->item(0)->getAttribute('name').'Controller';
				$controllerClass = $tempModelName.'Controller';
				//***************************************** modificado fim
				import(APP_NAME.'.controller.'.$controllerClass);
				$controller = new $controllerClass();
				$controllerEl->item(0)->parentNode->removeChild($controllerEl->item(0));
				$this->setController($controller);
				if ($model) {
					$controller->setModel($model);
				}
				//$controller->defineModelView($model,$this);
			}
		} else {
			$controllerClass = $viewName.'Controller';
			import(APP_NAME.'.controller.'.$controllerClass);
			$controller = new $controllerClass();
			$this->setController($controller);
			if ($model) {
				$controller->setModel($model);
			}
			//$controller->defineModelView($model,$this);
		}
		if (!defined('ENGINE_NO_CACHE')) {
			$this->domRawCache = $this->getDOMDocument()->saveXML();
		}
	}

	/**
	 * @return DOMDocument
	 */
	public function getDOMDocument() {
		return $this->domDocument;
	}

	/** Renderiza a saida de dados
	 * @return string
	 */
	public function render() {
		$els = $this->domDocument->getElementsByTagName('input');
		$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
		for ($i = $els->length; --$i >= 0; ) {
			$el = $els->item($i);
			preg_match_all($pattern,$el->getAttribute('value'),$valueArr);
			if (isset($valueArr['path'][0])) {
				$el->setAttribute('value',EntityUtils::getByPath($this->getModel(),$valueArr['path'][0]));
			}
		}
		//Parse Taglibs
		$teste = EngineWebComponentsParser::parse($this);
		 
		$doctype=$this->getDOMDocument()->implementation->createDocumentType("html","-//W3C//DTD XHTML 1.0 Strict//EN","http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd");
		$output=$this->getDOMDocument()->implementation->createDocument('','',$doctype);
		$output->preserveWhiteSpace = false;
		$output->formatOutput = true;
		$output->appendChild($output->importNode($this->getDOMDocument()->documentElement,true));
		$output->encoding='UTF-8';
		$output=$output->saveXML();

		(empty($_SERVER['HTTP_ACCEPT']) ? $tempHttpAccept = "" : $tempHttpAccept = $_SERVER['HTTP_ACCEPT']);
		(empty($_SERVER["HTTP_USER_AGENT"]) ? $tempHttpUserAgent = "" : $tempHttpUserAgent = $_SERVER["HTTP_USER_AGENT"]);

		$xhtml=preg_match(
		    '/application\/xhtml\+xml(?![+a-z])'.
		    '(;q=(0\.\d{1,3}|[01]))?/i',
		$tempHttpAccept,$xhtml) &&
		(isset($xhtml[2])?$xhtml[2]:1) > 0 ||
		strpos($tempHttpUserAgent,
		    "W3C_Validator")!==false ||
		strpos($tempHttpUserAgent,
		    "WebKit")!==false; // XHTML Content-Negotiation
			
		header('Content-Type: '.($xhtml?'application/xhtml+xml':'text/html').'; charset=UTF-8');
		echo $output;
	}

	/** Atualiza os valores dos campos com os da requisicao
	 * @return void
	 */
	public function getData() {
		$data = array();
		foreach ( $_REQUEST as $path => $value) {
			$el = $this->getDOMDocument()->getElementById($path);
			if (isset($el)) {
				switch ($el->localName) {
					case EngineSelectOne::TAG_NAME:
						$options = $el->getElementsByTagName(EngineSelectOptions::TAG_NAME);
						if (@$options->item(0)->localName == EngineSelectOptions::TAG_NAME) {
							$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
							preg_match_all($pattern,$options->item(0)->getAttribute('values'),$valueArr);
							if (isset($valueArr['path'][0])) {
								//$optValue = EntityUtils::getByPath($this->getModel(),$valueArr['path'][0]);
								$data[EntityUtils::parsePath($el->getAttribute('value'))] =  EntityUtils::getByPath($this->getModel(),$valueArr['path'][0])->get($value);
							}
							$pattern = "/^(\\\$\{)(?P<method>[\w\d\.]+)(\})$/";
							preg_match_all($pattern,$options->item(0)->getAttribute('values'),$valueArr);
							if (isset($valueArr['method'][0])) {
								//$optValue = $this->getModel()->{$valueArr['method'][0]}();
								$data[$path] = $this->getModel()->{$valueArr['method'][0]}()->get($value);
							}
							//preg_match_all($pattern,$el->childNodes->item(0)->getAttribute('values'),$valueArr);
							//$data[$path] = EntityUtils::getByPath($this->getModel(),$valueArr['path'][0])->get($value);
						} else {
							$data[$path] = $value;
						}
						break;
					case EngineSelectMany::TAG_NAME:
						if ($el->childNodes->item(0)->localName == EngineSelectOptions::TAG_NAME) {
							$pattern = "/^(\#\{)(?P<path>[\w\d\.]+)(\})$/";
							preg_match_all($pattern,$el->childNodes->item(0)->getAttribute('values'),$valueArr);
							if (isset($valueArr['path'][0])) {
								//$optValue = EntityUtils::getByPath($this->getModel(),$valueArr['path'][0]);
								$data[$path] =  EntityUtils::getByPath($this->getModel(),$valueArr['path'][0])->get($value);
							}
							$pattern = "/^(\\\$\{)(?P<method>[\w\d\.]+)(\})$/";
							preg_match_all($pattern,$el->childNodes->item(0)->getAttribute('values'),$valueArr);
							if (isset($valueArr['method'][0])) {
								//$optValue = $this->getModel()->{$valueArr['method'][0]}();
								$data[$path] = $this->getModel()->{$valueArr['method'][0]}()->get($value);
							}
							//preg_match_all($pattern,$el->childNodes->item(0)->getAttribute('values'),$valueArr);
							//$data[$path] = EntityUtils::getByPath($this->getModel(),$valueArr['path'][0])->get($value);
						} else {
							$data[$path] = $value;
						}
						break;
					case "inputBooleanCheckbox":
						if (stristr($el->getAttribute('value'),'currentElement')) {
							$pe = $el->parentNode;
							while ($pe->localName != "dataGrid") {
								$pe = $pe->parentNode;
								if ($pe->localName == "body") {
									continue;
								}
							}
							foreach ($value as $idx) {
								$data[EntityUtils::parsePath($pe->getAttribute('values')).'.['.$idx.']'.str_replace('currentElement','',EntityUtils::parsePath($el->getAttribute('value')))] = true;
							}
						}
						break;
					default:
						$data[$path] = $value;
						break;
				}
			}
		}
		return $data;
	}

	/** Carrega o template x(HT)ml
	 * @param string $path   Caminho do template
	 */
	private function loadDomDocument($path) {
		$dom = new DOMDocument();
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		//$dom->validateOnParse = true;

		if(@$dom->load(realpath("./".APP_PATH."/view/".$path."View.xhtml"))) {
			//if(@$dom->load(dirname(ENGINE_PATH)."/rs/view/".$path."View.xhtml")) {
			(empty($_SERVER['DOCUMENT_ROOT']) ? $tempDocumentRoot = "" : $tempDocumentRoot = $_SERVER['DOCUMENT_ROOT']);
			$doctype=$dom->implementation->createDocumentType("html","-//INTERFACEWEB//ELEMENTS XHTML EngineML 1.0//EN",$tempDocumentRoot."/lib/engine/dtd/xhtml-engine-1.dtd");
			$output=$dom->implementation->createDocument('','',$doctype);
			$output->preserveWhiteSpace = false;
			$output->formatOutput = true;
			@$output->appendChild($output->importNode($dom->documentElement,true));
			unset($dom);
			$this->processIncludes($output);
			@$output->validate();
			/*
			 $doctype=DOMImplementation::createDocumentType("html","-//INTERFACEWEB//ELEMENTS XHTML EngineML 1.0//EN",$_SERVER['"xhtml-engine-1.dtd");
			 $output=DOMImplementation::createDocument('','',$doctype);
			 $output->preserveWhiteSpace = false;
			 $output->formatOutput = true;
			 $output->appendChild($output->importNode($this->getDOMDocument()->documentElement,true));
			 $output->encoding='ISO-8859-1';
			 */
			$this->domDocument = $output;
		} else {
			throw new Exception("Nao foi possivel carregar o arquivo ".$path."View.xhtml");
			//Nao carregou o arquivo
		}
	}

	private function processIncludes($dom) {
		$includes = $dom->getElementsByTagNameNS(EngineWebComponentsParser::$ns,'include');
		if ($includes->length > 0) {
			$incDom = new DOMDocument();
			$incDom->preserveWhiteSpace = false;
			$incDom->formatOutput = false;
			for ($i = $includes->length; --$i >= 0; ) {
				$element = $includes->item($i);
				if ($element->getAttribute('type') == "xhtml") {
					$incDom->load(realpath("./".APP_PATH."/view/".$element->getAttribute('src')));
					$this->processIncludes($incDom);
					$body = $incDom->documentElement->getElementsByTagName('body');
					if ($body->length > 0) {
						for ($j = $body->item(0)->childNodes->length; --$j >= 0;) {
							$bodyEls = $body->item(0)->childNodes->item($j);
							$element->parentNode->insertBefore($dom->importNode($bodyEls,true),$element);
						}
					} else {
						$element->parentNode->insertBefore($dom->importNode($incDom->documentElement,true),$element);
					}
					$element->parentNode->removeChild($element);
				}
			}
		}
		return;
	}

	public function __wakeup() {
		if (defined('ENGINE_NO_CACHE')) {
			$this->loadDomDocument($this->path);
		} else {
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			@$dom->loadXML($this->domRawCache);
			$this->domDocument = $dom;
			@$this->domDocument->validate();
		}
	}
}

?>