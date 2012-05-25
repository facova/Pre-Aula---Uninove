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
 * @subpackage core
 * File: Setter.php
 **/


/** Classe Accessor para propriedades de objetos. Define o valor fornecido em um atributo.
 * @author Silas R. N. Junior
 */
class Setter {

	/** Caminho da propriedade a que este getter se refere
	 * @var string
	 */
	private $property;

	/** Nome da classe contendo a propriedade referenciada
	 * @var string
	 */
	private $class;

	/**
	 * @param mixed $class   Classe contendo o atributo
	 * @param string $property   Caminho da propriedade referenciada
	 */
	public function Setter($class, $property) {
		if (class_exists($class))
		$this->class = $class;
		else
		throw new Exception("Erro ao criar Setter: Classe inexistente [".$class."]");

		$rf = new ReflectionClass($this->class);
		if ($rf->hasProperty($property))
		$this->property = $property;
		else
		throw new Exception("Erro ao criar Setter: Classe ".$this->class." nao possui a propriedade [".$property."]");
	}

	/** Define o valor da propriedade.
	 * @param object $o   Objeto contendo a propriedade
	 * @param mixed $value   Valor a ser definido no objeto
	 * @return void
	 */
	public function set($o, $value) {
		if ($o instanceof $this->class) {
			$o->{EntityUtils::getSetter($this->property)}($value);
		} else {
			throw new Exception("Erro ao obter valor: O objeto fornecido n�o � da classe ".$this->class);
		}
	}
}

?>
