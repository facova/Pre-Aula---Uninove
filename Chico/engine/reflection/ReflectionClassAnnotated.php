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
 * File: ReflectionClassAnnotated.php
 **/

import('engine.annotations.Annotations');
import('engine.reflection.IAnnotatedReflectionClass');

/** Extensao da classe de reflexao implementando suporte a annotations
 * @author Silas R. N. Junior
 */
class ReflectionClassAnnotated extends ReflectionClass implements IAnnotatedReflectionClass {

	/** Annotations da classe
	 * @var array
	 */
	protected $annotations;

	/** Construtor da Classe de Reflexao com Annotations
	 * @param mixed $arguments
	 */
	public function ReflectionClassAnnotated($argument) {
		try {
			parent::__construct($argument);
			$parser = new Annotations();
			$this->annotations = $parser->parse($this->getDocComment());
		} catch (AnnotationException $e) {
			throw new ReflectionException("[Annotations] Problemas com as Annotations da classe ".parent::getName().".\n".$e->getMessage());
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/** Recupera a ReflectionClass da classe pai com suas Annotations
	 * @return ReflectionClassAnnotated
	 */
	public function getParentClassAnnotated() {
		try {
			if ($this->getParentClass()) {
				return new ReflectionClassAnnotated($this->getParentClass()->getName());
			} else {
				return false;
			}
		} catch (ReflectionException $e) {
			throw new ReflectionException("Problemas instanciando a classe de Reflexao pai da classe ".$this->getParentClass()->getName().".\n".$e->getMessage());
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/** Recupera uma ReflectionProperty com suas Annotations
	 * @param string $name   Nome da propriedade
	 * @return ReflectionPropertyAnnotated
	 */
	public function getPropertyAnnotated($name) {
		if ($this->hasProperty($name)) {
			try {
				return new ReflectionPropertyAnnotated($this->getName(),$name);
			} catch (Exception $e) {
				throw new ReflectionException("Problemas instanciando a classe de Reflexao para a propriedade ".parent::getName().".".$name.". \n".$e->getMessage());
			}
		} else {
			throw new ReflectionException("Problemas instanciando a classe de Reflexao para a propriedade ".parent::getName().".".$name.". A propriedade e inexistente.");
		}
	}

	/** Recupera as Classes de Propriedade da classe com suas annotations
	 * @return array
	 */
	public function getPropertiesAnnotated() {
		$arr = array();
		$props = $this->getProperties();
		foreach ($props as $property) {
			$arr[] = new ReflectionPropertyAnnotated($this->getName(),$property->getName());
		}
		return $arr;
	}

	/** Recupera uma ReflectionMethod com suas Annotations
	 * @param string $name   Nome da propriedade
	 * @return ReflectionMethodAnnotated
	 */
	public function getMethodAnnotated($name) {
		if ($this->hasMethod($name)) {
			return new ReflectionMethodAnnotated($this->getName(),$name);
		} else {
			throw new ReflectionException("Metodo [$name] inexistente para a classe [".parent::getName()."].");
		}
	}

	/** Recupera as Classes de Metodo da classe com suas annotations
	 * @return array
	 */
	public function getMethodsAnnotated() {
		$arr = array();
		$mets = $this->getMethods();
		foreach ($mets as $method) {
			$arr[] = new ReflectionMethodAnnotated($this->getName(),$method->getName());
		}
		return $arr;
	}

	/** Recupera uma Annotation
	 * @param string $name   Nome da Annotation
	 * @return Annotation
	 */
	public function getAnnotation($name) {
		if($this->annotations[$name]) {
			return $this->annotations[$name];
		} else {
			throw new ReflectionException("Annotation [$name] inexistente para a classe [".parent::getName()."]");
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

	/** Recupera uma Lista das Annotations da classe
	 * @return array
	 */
	public function getAnnotations() {
		return $this->annotations;
	}
}

?>