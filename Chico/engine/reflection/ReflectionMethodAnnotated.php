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
 * File: ReflectionMethodAnnotated.php
 **/

import('engine.reflection.ReflectionClassAnnotated');

/** Construtor de Classe de Reflexao de Metodo com Annotations
 * @author Silas R. N. Junior
 */
class ReflectionMethodAnnotated extends ReflectionMethod implements IAnnotatedReflectionClass {

	/** Annotations do Metodo
	 * @var array
	 */
	private $annotations;

	/** Construtor da Classe de Reflexao com Annotations
	 * @param mixed $class_or_method
	 * @param string[optional] $name
	 */
	public function ReflectionMethodAnnotated($class_or_method, $name) {
		try {
			parent::__construct($class_or_method,$name);
			$parser = new Annotations();
			$this->annotations = $parser->parse($this->getDocComment());
		} catch (AnnotationException $e) {
			throw new ReflectionException("Problemas com as Annotations da classe [".parent::getDeclaringClass()->getName()."] no metodo [".$name."].\n".$e->getMessage());
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/** Recupera uma Annotation
	 * @param string $name   Nome da Annotation
	 * @return Annotation
	 */
	public function getAnnotation($name) {
		if($this->annotations[$name]) {
			return $this->annotations[$name];
		} else {
			throw new Exception("Annotation [$name] inexistente para o metodo [$this->getName()] na classe [$this->getDeclaringClass()->getName()].");
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

	/** Recupera uma Lista das Annotations da entidade
	 * @return array
	 */
	public function getAnnotations() {
		return $this->annotations;
	}
}

?>
