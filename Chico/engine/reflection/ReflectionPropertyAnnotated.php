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
 * @subpackage reflection
 * File: ReflectionPropertyAnnotated.php
 **/

import('engine.reflection.IAnnotatedReflectionClass');

/**
 * @author Silas R. N. Junior
 */
class ReflectionPropertyAnnotated extends ReflectionProperty implements IAnnotatedReflectionClass {

	/** Annotations da propriedade
	 * @var array
	 */
	protected $annotations;

	/** Construtor da Classe de Reflexao com Annotations
	 * @param mixed $class
	 * @param string $name
	 */
	public function ReflectionPropertyAnnotated($class, $name) {
		parent::__construct($class,$name);
		$parser = new Annotations();
		$this->annotations = $parser->parse($this->getDocComment());
	}

	/** Recupera uma Annotation
	 * @param string $name   Nome da Annotation
	 * @return Annotation
	 */
	public function getAnnotation($name) {
		if($this->annotations[$name]) {
			return $this->annotations[$name];
		} else {
			throw new Exception("Annotation [{$name}] inexistente para a propriedade [{$this->getName()}] na classe [{$this->getDeclaringClass()->getName()}].");
		}
	}

	/** Verifica a existencia de uma Annotation
	 * @param string $name   Nome da Annotation
	 * @return boolean
	 */
	public function hasAnnotation($name) {
		if(isset($this->annotations[$name])) {
			return true;
		} else {
			return false;
		}
	}

	/** Recupera uma Lista das Annotations da propriedade
	 * @return array
	 */
	public function getAnnotations() {
		return $this->annotations;
	}
}

?>