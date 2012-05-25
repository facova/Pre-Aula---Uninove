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
 * @subpackage db
 * File: EntityUtils.php
 **/


/** Utilitarios para obtencao de informacoes e realizacao de operacoes com Entidades.
 * @author Silas R. N. Junior
 */
class EntityUtils {

	/** Gera uma string "getter" para a propriedade fornecida.
	 * @param mixed $property   String/ReflectionMethod do nome da propriedade
	 * @return string
	 */
	public static function getGetter($property) {
		if ($property instanceof ReflectionPropertyAnnotated) {
			$propertyName = $property->getName();
			if ($property->hasAnnotation('var') && trim($property->getAnnotation('var')) == "boolean") $bool = true;
		} else {
			$propertyName = $property;
		}
		if (strlen($propertyName) > 0) {
			$a = substr($propertyName,0,1);
			$ttribute = substr($propertyName,1);
			$a = strtoupper($a);
			if (isset($bool)) {
				$getter = 'is'.$a.$ttribute;
			} else {
				$getter = 'get'.$a.$ttribute;
			}
		} else {
			throw new Exception('Atributo nulo ao gerar Getter.');
		}
		return $getter;
	}

	/** Gera uma string "setter" para a propriedade fornecida.
	 * @param mixed $property   String/ReflectionMethod do nome da propriedade
	 * @return string
	 */
	public static function getSetter($property) {
		if ($property instanceof ReflectionPropertyAnnotated) {
			$propertyName = $property->getName();
		} else {
			$propertyName = $property;
		}
		if (strlen($propertyName) > 0) {
			$a = substr($propertyName,0,1);
			$ttribute = substr($propertyName,1);
			$a = strtoupper($a);
			$setter = 'set'.$a.$ttribute;
		} else {
			throw new Exception('Atributo nulo ao gerar Setter.');
		}
		return $setter;
	}

	/** Retorna um array indexado plas strings da estrutura de objetos componentes da entidade com seu respectivo tipo como valor.
	 * @param object $object   Objeto para obter os dados
	 * @param string $current   String correspondente a propriedade contendo os atributos. Utilizado nas chamadas recursivas do metodo
	 * @return ArrayList
	 * @deprecated
	 */
	public static function getPropertiesStringPath($object, $current) {
		/** <TODO> Implement. */
	}

	/** Recupera as propriedades da Entidade
	 * @param string $class Nome da classe
	 * @return array
	 */
	public static function getProperties($class) {
		if ($class instanceof ReflectionClassAnnotated) {
			$rca = $class;
		} else if (class_exists($class)) {
			$rca = new ReflectionClassAnnotated($class);
		} else {
			throw new Exception("Classe {$class} inexistente.");
		}
		$this->properties = $rca->getPropertiesAnnotated();
		return $this->properties;
	}

	/** Retorna o nome da coluna da propriedade
	 * @param string $property   Nome da propriedade
	 * @param string $class   Nome da classe contendo a propriedade
	 * @return string
	 */
	public static function getPropertyColumnName($class,$property) {
		if ($class instanceof ReflectionClassAnnotated)
		$rca = $class;
		else if (class_exists($class))
		$rca = new ReflectionClassAnnotated($class);
		else
		throw new Exception("Classe {$class} inexistente.");
			
		$prop = $rca->getPropertyAnnotated($property);
			
		if ($prop->hasAnnotation("Column"))
		$column = $prop->getAnnotation("Column");
		else if ($prop->hasAnnotation("JoinColumn"))
		$column = $prop->getAnnotation("JoinColumn");

		$rm = $rca->getMethodAnnotated(EntityUtils::getGetter($prop));
		if ($rm->hasAnnotation("Column"))
		$column = $rm->getAnnotation("Column");
		else if ($rm->hasAnnotation("JoinColumn"))
		$column = $rm->getAnnotation("JoinColumn");

		if (!isset($column))
		return $prop->getName();
		else
		return $column->getName();
	}

	/** Retorna o tipo de dado atribuido a propriedade
	 * @param string $class   Nome da Classe
	 * @param string $name   Nome da propriedade
	 * @return string
	 */
	public static function getPropertyType($class, $name) {
		if ($class instanceof ReflectionClassAnnotated) {
			$rca = $class;
		} else if (class_exists($class)) {
			$rca = new ReflectionClassAnnotated($class);
		} else {
			throw new Exception("Classe {$class} inexistente.");
		}
		if ($rca->hasProperty($name)) {
			$prop = $rca->getPropertyAnnotated($name);
			if ($rca->hasMethod(EntityUtils::getGetter($name))) {
				$rm = $rca->getMethodAnnotated(EntityUtils::getGetter($name));
				if ($rm->hasAnnotation('OneToMany')) {
					if (strlen($rm->getAnnotation('OneToMany')->getTargetEntity()) > 0) {
						return $rm->getAnnotation('OneToMany')->getTargetEntity();
					} else {
						throw new Exception("A entidade [$class] com a propriedade [$name] possui uma anotacao @OneToMany sem uma targetEntity definida");
					}
				} else if ($rm->hasAnnotation('ManyToMany')) {
					if (strlen($rm->getAnnotation('ManyToMany')->getTargetEntity()) > 0) {
						return $rm->getAnnotation('ManyToMany')->getTargetEntity();
					} else {
						throw new Exception("A entidade [$class] com a propriedade [$name] possui uma anotacao @ManyToMany sem uma targetEntity definida");
					}
				}
			}
			if ($prop->hasAnnotation('ManyToOne')) {
				if (strlen($prop->getAnnotation('ManyToOne')->getTargetEntity()) > 0) {
					return $prop->getAnnotation('ManyToOne')->getTargetEntity();
				} else {
					throw new Exception("A entidade [$class] com a propriedade [$name] possui uma anotacao @ManyToOne sem uma targetEntity definida");
				}
			} else if ($prop->hasAnnotation('ManyToMany')) {
				if (strlen($prop->getAnnotation('ManyToMany')->getTargetEntity()) > 0) {
					return $prop->getAnnotation('ManyToMany')->getTargetEntity();
				} else {
					throw new Exception("A entidade [$class] com a propriedade [$name] possui uma anotacao @ManyToMany sem uma targetEntity definida");
				}
			} else {
				if ($prop->hasAnnotation('var')) {
					return trim($prop->getAnnotation('var'));
				} else {
					return "string";
				}
			}
		} else {
			throw new Exception("A entidade ".$rca->getName()." nao possui uma propriedade de nome {$name}.");
		}
	}

	/** Retorna o nome do campo Identificador
	 * @param string $class   Nome da Classe
	 * @return string
	 */
	public static function getIdFieldName($class) {
		if ($class instanceof ReflectionClassAnnotated) {
			$rca = $class;
		} else if (class_exists($class)) {
			$rca = new ReflectionClassAnnotated($class);
		} else {
			throw new Exception("Classe {$class} inexistente.");
		}
		return self::getPropertyColumnName($class,self::getIdPropertyName($class));
	}

	/** Retorna o nome da propriedade identificadora da Entidade
	 * @param string $class   Nome da Classe
	 * @return string
	 */
	public static function getIdPropertyName($class) {
		if ($class instanceof ReflectionClassAnnotated) {
			$rca = $class;
		} else if (class_exists($class)) {
			$rca = new ReflectionClassAnnotated($class);
		} else {
			throw new Exception("Classe {$class} inexistente.");
		}
		if (!isset($idPropertyName)) {
			$duplicate = array();
			foreach ($rca->getPropertiesAnnotated() as $property) {
				if ($property->hasAnnotation("Id")) {
					if (isset($idPropertyName)) {
						$duplicate = $property->getName();
						//throw new Exception("A Entidade {$this->getClass()->getName()} possui dois ou mais campos como chave Identificadora []. Remova a anotacao @Id das propriedades nao identificadoreas.");
					}
					$idPropertyName[] = $property->getName();
				}
				if (!$rca->hasMethod(EntityUtils::getGetter($property))) {
					continue;
				}
				$rm = $rca->getMethodAnnotated(EntityUtils::getGetter($property));
				if ($rca->hasMethod(EntityUtils::getSetter($property->getName()))) {
					if ($rm->hasAnnotation("Id")) {
						if (isset($idPropertyName) && ($property->getName() != $idPropertyName)) {
							$duplicate = $property->getName();
							//throw new Exception("A Entidade {$this->getClass()->getName()} possui dois ou mais campos como chave Identificadora []. Remova a anotacao @Id das propriedades nao identificadoreas.");
						}
						$idPropertyName[] = $property->getName();
					}
				}
			}
			/*
			 if (count($duplicate) > 0) {
			 $duplicate = implode(",",$duplicate);
				throw new Exception("A Entidade {$this->getClass()->getName()} possui dois ou mais campos como chave Identificadora [{$duplicate}]. Remova a anotacao @Id das propriedades nao identificadoreas.");
				}
				*/
		}
		if (count($idPropertyName) == 0) {
			throw new Exception("A entidade ".$rca->getName()." nao possui um campo identificador definido. Utilize @Id no getter correspondente a propriedade.");
		}
		if (count($idPropertyName) > 1) {
			return $idPropertyName;
		} else {
			return $idPropertyName[0];
		}
	}

	/** Retorna o nome da tabela da entidade
	 * @param string $class   Nome da Classe
	 * @return string
	 */
	public static function getTableName($class) {
		if ($class instanceof ReflectionClassAnnotated) {
			$rca = $class;
		} else if (class_exists($class)) {
			$rca = new ReflectionClassAnnotated($class);
		} else {
			throw new Exception("Classe {$class} inexistente.");
		}
		return $rca->hasAnnotation('Table') ? $rca->getAnnotation('Table')->getName() : $rca->getName();
	}

	/** Retorna o valor corresponente ao caminho fornecido
	 * @param object $object   Objeto
	 * @param string $path   Caminho do atributo
	 * @return mixed
	 */
	public static function getByPath($object, $path) {
		if (!isset($path)||(strlen($path) == 0))
		return false;
		$rf = new ReflectionClass($object);
		if(stristr($path,".")) {
			$splitPath = explode(".",$path);
		} else if(stristr($path,"-")) {
			$splitPath = explode("-",$path);
		} else {
			if (strtolower($path) == "currentelement") {
				return $object;
			} else if ($rf->hasMethod(self::getGetter($path))) {
				return $object->{self::getGetter($path)}();
			} else {
				if (is_callable(array($object,self::getGetter($path)))) {
					return $object->{self::getGetter($path)}();
				} else {
					return false;
				}
			}
		}
			
		foreach ($splitPath as $pi) {
			if (isset($value)) {
				$rf = new ReflectionClass($value);
				if ($rf->hasMethod(self::getGetter($pi))) {
					$value = $value->{self::getGetter($pi)}();
				} else {
					preg_match("/^(\[)(?P<idx>[\d]+)(\])$/",$pi,$matches);
					if (isset($matches['idx'][0])) {
						$value = $value->get($matches['idx'][0]);
					} else if (is_callable(array($value,self::getGetter($pi)))) {
						$value = $value->{self::getGetter($pi)}();
					} else {
						return false;
					}
				}
			} else {
				if (strtolower($pi) == "currentelement") {
					$value = $object;
				} else if ($rf->hasMethod(self::getGetter($pi))) {
					$value = $object->{self::getGetter($pi)}();
				} else {
					preg_match("/^(\[)(?P<idx>[\d]+)(\])$/",$pi,$matches);
					if (isset($matches['idx'][0])) {
						$value = $value->get($matches['idx'][0]);
					}
					if (is_callable(array($object,self::getGetter($pi)))) {
						$value = $object->{self::getGetter($pi)}();
					} else {
						return false;
					}
				}
			}
		}
		$rf = null;
		return $value;
	}

	/**
	 * @param object $object   Objeto
	 * @param string $path   Caminho do atributo
	 * @param mixed $value   Valor a ser setado
	 * @return boolean
	 */
	public static function setByPath($object, $path, $value) {
		if (!isset($path)||(strlen($path) == 0))
		return false;
		$rf = new ReflectionClass($object);
		if(stristr($path,".")) {
			$splitPath = explode(".",$path);
		} else if(stristr($path,"-")) {
			$splitPath = explode("-",$path);
		} else {
			if ($rf->hasMethod(self::getSetter($path))) {
				$object->{self::getSetter($path)}($value);
				return true;
			} else {
				while ($rf->getParentClass()) {
					$rf = $rf->getParentClass();
					if ($rf->hasMethod(self::getSetter($path))) {
						$object->{self::getSetter($path)}($value);
						return true;
					}
				}
				return false;
			}
		}
		$setProp = array_pop($splitPath);
		$o = self::getByPath($object,implode(".",$splitPath));
		if ($o) {
			$ro = new ReflectionClass($o);
			if (is_callable(array($o,self::getSetter($setProp)))) {
				$o->{self::getSetter($setProp)}($value);
			} else {
				return false;
			}
		} else {
			return false;
		}
		/*
		 if ($ro->hasMethod(self::getSetter($setProp))) {
			$o->{self::getSetter($setProp)}($value);
			} else {
			while ($rf->getParentClass()) {
			$rf = $rf->getParentClass();
			if ($rf->hasMethod(self::getSetter($setProp))) {
			$o = self::getByPath($object,implode(".",$splitPath));
			if ($o) {
			$o->{self::getSetter($setProp)}($value);
			return true;
			}
			}
			}
			return false;
			}
			*/
		return true;
	}

	/** Retorna o valor corresponente ao caminho fornecido
	 * @param object $object   Objeto
	 * @param string $path   Caminho do atributo
	 * @return mixed
	 */
	public static function getValueByPath($object, $path) {
		$valueArr = array();
		$pattern = "/^(\#\{)(?P<path>[\w\d\.\[\]]+)(\})$/";
		preg_match_all($pattern,$path,$valueArr);
		if (isset($valueArr['path'][0])) {
			return self::getByPath($object,$valueArr['path'][0]);
		}
		$pattern = "/^(\\\$\{)(?P<method>[\w\d\.\[\]]+)(\})$/";
		preg_match_all($pattern,$path,$valueArr);
		if (isset($valueArr['method'][0])) {
			return $object->{$valueArr['method'][0]}();
		}
		return self::getByPath($object,$path);
	}

	public static function parsePath($path) {
		$valueArr = array();
		$pattern = "/^(\#\{)(?P<path>[\w\d\.\[\]]+)(\})$/";
		preg_match_all($pattern,$path,$valueArr);
		if (isset($valueArr['path'][0])) {
			//return new PropertyPath($valueArr['path'][0]);
			return $valueArr['path'][0];
		}
		$pattern = "/^(\\\$\{)(?P<method>[\w\d\.\[\]]+)(\})$/";
		preg_match_all($pattern,$path,$valueArr);
		if (isset($valueArr['method'][0])) {
			//return new MethodPath($valueArr['method'][0]);
			return $valueArr['method'][0];
		}
	}

	/** Retorna a string identificadora da entidade
	 * @param object $object   Entidade
	 * @return string
	 */
	public static function getIdString($object) {
		$classMeta = EntityManager::getClassMeta(get_class($object));
		$indexName = $classMeta->getReflectionClass()->getName();
		if (sizeOf($classMeta->getIndexMeta()) == 0) {
			if ($classMeta->hasSuper()) {
				$sp = $classMeta->getSuperMeta();
				while ($sp->hasSuper()) $sp = $sp->getSuperMeta();
				$qeIdx = $sp->getIndexMeta();
			} else {
				die ("verificar geração de indices");
			}
		} else {
			$qeIdx = $classMeta->getIndexMeta();
		}
		foreach ($qeIdx as $property => $column) {
			if ($classMeta->getReflectionClass()->hasMethod(EntityUtils::getGetter($property))) {
				$indexName .= "__".$object->{EntityUtils::getGetter($property)}();
			} else {
				$indexName .= "__".$object->{$property->getName()};
			}
		}
		return $indexName;
	}
}
?>