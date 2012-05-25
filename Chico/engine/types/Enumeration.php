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
 * @subpackage types
 * File: Enumeration.php
 **/

/** Enumeracoes de valores
 * @author:		Jim Sierra/Sierra Web & Technology Solutions
 * @homepage:	www.sierrawebtech.com
 * @license : 		GNU/General Public License
 * 			License can be viewed at http://www.opensource.org/licenses/gpl-license.php
 * COPYRIGHT (c) 2008 Jim Sierra
 *
 * Modified By
 * @author Silas R. N. Junior
 */

import('engine.types.Type');
import('engine.exceptions.EnumerationException');

class Enumeration extends Type {

	private $enumArray;
	protected $value = '';
	private $init = false;
	private static $flag = false; //modificacao

	public function __set($key,$value) {
		if (!$this->init) {
			$this->init();
		}
		// Is this a valid field
		if ($key != 'value')
		throw new EnumerationException('Uma enumeracao pode conter somente a propriedade "value".');
	  


		/**********************modificacao*********************/
		if (is_string($value))
		{
			if (in_array((string) $value, $this->enumArray) && self::$flag==false)
			{
				self::$flag = true;
				$className = get_class($this); //obter a classe filha
				$value = new $className($value); //criar a classe filha como objeto
			}
		}
		/**********************modificacao*********************/
	  
	  
	  
		if (is_int($value)) {
			if (isset($this->enumArray[$value])) {
				$this->value = $this->enumArray[$this->enumArray[$value]];
				return;
			} else {
				throw new EnumerationException('Ordinal inexistente para a Enumera��o.');
			}
		}
		// Is value a proper enumeration value
		if (!in_array((string) $value, $this->enumArray))
		throw new EnumerationException('Valor invalido para a propriedade.');

		$this->value = $value;

	}

	public function __get($key) {

		if (!$this->init) {
			$this->init();
		}
		// Is this a valid field
		if ($key != 'value')
		throw new EnumerationException('Uma enumeracao pode conter somente a propriedade "value".');

		return $this->value;

	}

	public function __construct()
	{
		// Get enumeration keys and values from child class constant values
		//Removing the necessity of a constructor in subclasses
		$rf = new ReflectionObject($this);
		$x = new ReflectionClass($rf->getName());
		$i = 0;
		foreach($x->getConstants() as $key=>$value) {
			$this->enumArray[$key] = $value;
			$this->enumArray["#_".$value] = $i;
			$this->enumArray[$i] = $key;
			$i++;
		}
	}

	public function __toString() {
		return (string)$this->__get('value');
	}

	private function init() {
		self::__construct();
		$this->init = true;
		$this->__set('value',$this->value);
	}

	public function setValue($value) {
		$this->__set('value',$value);
	}

	public function getValue() {
		return $this->__get('value');
	}

	public function ordinal() {
		return $this->enumArray["#_".$this->__get('value')];
	}

	public function toArray() {
		if (!$this->init)
		$this->init();
		return $this->enumArray;
	}
}

?>