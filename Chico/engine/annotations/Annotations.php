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
 * @subpackage annotations
 * File: Annotations.php
 **/

import('engine.annotations.Annotation');
import('engine.annotations.*');
import('engine.db.JoinType');
import('engine.db.FetchType');
import('engine.db.CascadeType');
import('engine.exceptions.AnnotationException');

/** Gera Objetos de Anotacoes.
 * @author Silas R. N. Junior
 */
class Annotations {

	/** Valores a serem ignorados como nome de anotacoes
	 * @var array
	 */
	public static $ignore = array("var", "deprecated", "return", "param", "author", "access", "since", "version", "abstract");

	/** Recupera as Annotations de uma string fornecida
	 * @param string $s   String padrao DocComment
	 * @return array
	 */
	public function parse($s) {
		$annotMatches = $this->matchAnnotations($s);
		$annotations = $this->createAnnotations($annotMatches);
		return $annotations;
	}

	/** Recupera uma string delimitando uma Annotation
	 * @param string $string
	 * @return array
	 */
	private function matchAnnotations($string) {
		//$pattern = "/(@(?P<annotation>[\w\d]+))(?P<parameters>([\s]*\([\s\w\d\=\'\"\,\.,\{,\}\*]*\)[\s\n\b])|(\s[\w\d\=\'\"\,\.]+[\s\n\b]))?/";
		$pattern = "/(\@(?P<annotation>[\w\d]+))(?P<parameters>([\s]*\(([^()]+|(?R))*\))|(\s[\w\d\=\'\"\,\.]+[\s\n\b]*))?/";
		preg_match_all($pattern,$string,$matches);
		return $matches;
	}

	/** Recupera os parametros delimitados em uma String
	 * @param string $string
	 * @return array
	 */
	private function matchParameters($string) {
		//$pattern = "/(([\s]*[\,]?(?P<parameter>([\w\d]+))?[\s]*[\=]?[\s]*))([\s]*(?P<value>([\'\"]?[\w\d\.]+\@[\w\d\.]+[\'\"]?)|([\'\"]?[\w\d\.]+[\'\"]?)|(\{[\w\d\,\"\.]+\})))[\,]?/";
		//$pattern = "/\((([\s]*[\,]?(?P<parameter>([^\@][\w\d]+))?[\s]*[\=]?[\s]*))([\s]*(?P<value>([\s\w\d\=\'\"\,\.,\{,\}\*\@\(\)]*)|([\'\"]?[\w\d\.]+\@[\w\d\.]+[\'\"]?)|([\'\"]?[\w\d\.]+[\'\"]?)|(\{[\w\d\,\"\.]+\})))[\,]?\)/";
		$pattern = "/(([\s]*[\,]?(?P<parameter>([\w\d]+))?[\s]*[\=]?))([\s]*(?P<value>(\{[\=\w\d\,\"\.\@\(\)]*\})|([\'\"][\w\d\.]+\@[\w\d\.]+[\'\"])|(\@[\w\d\.]+\([\w\d\.\"\'\(\)\=\,]*\))|([\'\"]?[\w\d\.]+[\'\"]?)))?[\,]?/";
		//$pattern = "/((?P<parameters>\(([^()]+|(?R))*\))/";
		preg_match_all($pattern,$string,$matches);
		return $matches;
	}

	/** Cria os Objetos de Annotations
	 * @param array $matches   Array com as ocorrencias e parametros
	 * @return array
	 */
	private function createAnnotations($matches) {
		$annot = $matches['annotation'];
		$param = $matches['parameters'];
		$ignore = Annotations::$ignore;
		$annotations = array();
		foreach ( array_keys($annot) as $idx) {
			$name = $annot[$idx];
			if (!in_array(strtolower($name),$ignore)) {
				$className = $name."Annotation";
				try {
					$rf = new ReflectionClass($className);
				} catch (Exception $e) {
					throw new AnnotationException("Annotation $className inexistente ou nao carregada");
				}
				$annotObj = new $className();
				$classParameters = $this->matchParameters($param[$idx]);
				if (strlen($param[$idx]) > 0 && count($classParameters) == 0) {
					throw new AnnotationException("Annotation @{$name} possui um erro de declaracao.");
				}
				$parameterName = $classParameters['parameter'];
				$parameterValue = $classParameters['value'];
				foreach ( array_keys($parameterName) as $paramIdx) {
					if (strlen($parameterName[$paramIdx]) > 0) {
						if ($rf->hasMethod(EntityUtils::getSetter($parameterName[$paramIdx]))) {
							$annotObj->{EntityUtils::getSetter($parameterName[$paramIdx])}($this->parseValues($parameterValue[$paramIdx]));
						} else {
							throw new AnnotationException("Annotation @{$name} nao possui o atributo $parameterName[$paramIdx] ou o metodo ".EntityUtils::getSetter($parameterName[$paramIdx]));
						}
					} else if (strlen($parameterValue[$paramIdx]) > 0) {
						$annotObj->setValue(trim($parameterValue[$paramIdx]));
					}
				}
				$annotations[$name] = $annotObj;
				$annotObj = null;
			} else {
				$annotations[$name] = trim($param[$idx]);
			}
		}
		return $annotations;
	}

	/** Processa os valores contidos na string
	 * @param string $string   String com o valor a ser processado
	 * @return mixed
	 */
	public function parseValues($string) {
		if (($string[0] == "\"")&&($string[strlen($string)-1] == "\"")) {
			//String
			$string = trim(trim($string),"\"");
		} else if (($string[0] == "{")&&($string[strlen($string)-1] == "}")) {
			//Array
			$string = explode(",",trim($string,"{}"));
			foreach ($string as &$item) {
				$item = $this->parseValues($item);
			}
		} else if ($string[0] == "@") {
			//Annotation
			$annot = $this->parse($string);
			return count($annot) > 1 ? $annot : array_pop($annot);
		} else if (($string == "true")||($string == "false")) {
			//Boolean
			return $string == "true" ? true : false;
		} else {
			//@InheritanceType
			if (stristr($string,"InheritanceType")) {
				$sarr = explode(".",$string);
				$const = $sarr[1];
				if ($const == InheritanceType::TABLE_PER_CLASS) {
					$string = InheritanceType::TABLE_PER_CLASS;
				} else if ($const == InheritanceType::TABLE_PER_SUBCLASS) {
					$string = InheritanceType::TABLE_PER_SUBCLASS;
				} else if ($const == InheritanceType::SINGLE_TABLE) {
					$string = InheritanceType::SINGLE_TABLE;
				}
			}
			//@FetchType
			if (stristr($string,"FetchType")) {
				$sarr = explode(".",$string);
				$const = $sarr[1];
				if ($const == FetchType::FETCH) {
					$string = FetchType::FETCH;
				} else if ($const == FetchType::LAZY) {
					$string = FetchType::LAZY;
				}
			}
			//@CascadeType
			if (stristr($string,"CascadeType")) {
				$sarr = explode(".",$string);
				$const = $sarr[1];
				if ($const == CascadeType::ALL) {
					$string = CascadeType::ALL;
				} else if ($const == CascadeType::CREATE) {
					$string = CascadeType::CREATE;
				} else if ($const == CascadeType::DELETE) {
					$string = CascadeType::DELETE;
				} else if ($const == CascadeType::SAVE) {
					$string = CascadeType::SAVE;
				} else if ($const == CascadeType::UPDATE) {
					$string = CascadeType::UPDATE;
				} else if ($const == CascadeType::NONE) {
					$string = CascadeType::NONE;
				}
			}
			//@CascadeType
			if (stristr($string,"GenerationType")) {
				$sarr = explode(".",$string);
				$const = $sarr[1];
				if ($const == GenerationType::AUTO) {
					$string = GenerationType::AUTO;
				} else if ($const == GenerationType::MAX) {
					$string = GenerationType::MAX;
				}
			}
		}
		return is_array($string) ? $string : trim($string);
	}
}

?>