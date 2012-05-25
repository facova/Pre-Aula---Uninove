<?php
/**
 * Engine PHP Application Framework
 * http://seelaz.com.br
 *
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
 * File: ReflectionORMProperty.php
 **/

import('engine.reflection.ReflectionPropertyAnnotated');

/** Classe de reflexao de propriedades ORM
 * @author Silas R. N. Junior
 */
class ReflectionORMProperty extends ReflectionPropertyAnnotated {

	/** Objeto de reflexao do getter
	 * @var ReflectionMethodAnnotated
	 */
	private $rflctGetter;

	/** Objeto de reflexao do setter
	 * @var ReflectionMethodAnnotated
	 */
	private $rflctSetter;

	/** Classe ORM que contem a propriedade
	 * @var ReflectionORMClass
	 */
	private $declaringORMClass;

	private $parentProperty;

	private $foreignIndexORMProperties;

	public function ReflectionORMProperty($class, $name) {

		parent::__construct($class,$name);

		//Verifica se o parametro do construtor é o objeto de reflexao de uma classe
		if (func_num_args() == 3) {
			$arg2 = func_get_arg(2);
			if ($arg2 instanceof ReflectionORMClass) $this->declaringORMClass = $arg2;
		} else if (func_num_args() == 4) {
			$arg2 = func_get_arg(2);
			$arg3 = func_get_arg(3);
			if ($arg2 instanceof ReflectionORMClass) $this->declaringORMClass = $arg2;
			if ($arg3 instanceof ReflectionORMProperty) $this->parentProperty = $arg3;
		} else {
			$this->declaringORMClass = new ReflectionORMClass($class);
		}

		if ($this->getDeclaringClass()->hasMethod("get".ucwords($this->getName()))) $this->rflctGetter = new ReflectionMethodAnnotated($this->getDeclaringClass()->getName(),"get".ucwords($this->getName()));
		if ($this->getDeclaringClass()->hasMethod("is".ucwords($this->getName()))) $this->rflctGetter = new ReflectionMethodAnnotated($this->getDeclaringClass()->getName(),"is".ucwords($this->getName()));
		if ($this->getDeclaringClass()->hasMethod("set".ucwords($this->getName()))) $this->rflctSetter = new ReflectionMethodAnnotated($this->getDeclaringClass()->getName(),"set".ucwords($this->getName()));
		
		//Validacoes
		if ($this->isJoined() && !in_array( $this->getType(), get_declared_classes() )) throw new ORMException("Erro na propriedade [".$this->getDeclaringClass()->getName().".".$this->getName()."] o tipo ".$this->getType()." nao e valido ou nao foi incluido");

	}

	/**
	 * @return boolean
	 */
	public function isColumn() {
		if (!$this->isJoined() && !$this->isCollection()) return true;
		return false;
	}

	/**
	 * @return boolean
	 */
	public function isJoined() {
		if ($this->hasAnnotation("OneToOne")) return true;
		if ($this->hasAnnotation("ManyToOne")) return true;
		return false;
	}

	/**
	 * @return boolean
	 */
	public function isCollection() {
		if ($this->hasAnnotation("OneToMany")) return true;
		if ($this->hasAnnotation("ManyToMany")) return true;
		return false;
	}

	/**
	 * @return boolean
	 */
	public function isIndex() {
		if ($this->hasAnnotation("Id")) return true;
		return false;
	}

	/**
	 * @return boolean
	 */
	public function isTransient() {
		if ($this->hasAnnotation("Transient")) return true;
		return false;
	}

	/** Verifica se a coluna deve possuir valores unicos
	 * @return boolean
	 */
	public function isUnique() {
		return $this->getColumn()->isUnique();
	}

	/** Verifica se a coluna deve possuir valores nulos
	 * @return boolean
	 */
	public function isNullable() {
		return $this->getColumn()->isNullable();
	}

	/** Verifica se a coluna deve participar de operacoes Insert
	 * @return boolean
	 */
	public function isInsertable() {
		return $this->getColumn()->isInsertable();
	}

	/** Verifica se a coluna deve participar de operacoes Update
	 * @return boolean
	 */
	public function isUpdatable() {
		return $this->getColumn()->isUpdatable();
	}

	/** Verifica se o framework deve excluir as entidades ao remover da colecao
	 * @return boolean
	 */
	public function isDeleteOrphan() {
		if ($this->isCollection()) {
			if ($this->hasAnnotation("OneToMany")) return $this->getAnnotation("OneToMany")->isDeleteOrphan();
			if ($this->hasAnnotation("ManyToMany")) return $this->getAnnotation("ManyToMany")->isDeleteOrphan();
		} else {
			throw new ORMException("A propriedade ".$this->getName()." nao esta mapeada como colecao");
		}
	}

	/**
	 * @return string
	 */
	public function getColumnName() {
		return $this->getColumn()->getName();
	}

	/** Retorna a estrategia de propagacao definida para as entidades
	 * @return mixed
	 */
	public function getCascade() {
		return $this->getAssociation()->getCascade();
	}

	/** Retorna a estrategia de recuperacao definida para as entidades
	 * @return mixed
	 */
	public function getFetch() {
		return $this->getAssociation()->getFetch();
	}

	/** Retorna o nome do campo na entidade mapeada que mapeia a entidade referenciadora
	 * @return string
	 */
	public function getMappedBy() {
		return $this->getAssociation()->getMappedBy();
	}

	/** Retorna a classe da entidade referenciada
	 * @return string
	 */
	public function getTargetEntity() {
		return $this->getAssociation()->getTargetEntity();
	}

	/** Retorna a profundidade do relacionamento a inicializar
	 * @return int
	 */
	public function getDepth() {
		return $this->getAssociation()->getDepth();
	}

	/** Retorna o nome da tabela intermediaria do relacionamento
	 * @return string
	 */
	public function getJoinTable() {
		if ($this->hasAnnotation("JoinTable")) {
			return $this->getAnnotation("JoinTable")->getName();
		} else {
			die("[ORM] Sem defaults para JoinTable. Declarar @JoinTable na propriedade [".$this->getDeclaringClass()->getName().".".$this->getName()."]");
		}
	}

	/** Retorna a(s) coluna(s) referente(s) a esta entidade na tabela de relacionamento
	 * @return string
	 */
	public function getJoinColumns() {
		if ($this->hasAnnotation("JoinTable")) {
			return $this->getAnnotation("JoinTable")->getJoinColumns();
		} else {
			die("[ORM] Sem defaults para JoinTable. Declarar @JoinTable na propriedade [".$this->getDeclaringClass()->getName().".".$this->getName()."]");
		}
	}

	/** Retorna a(s) coluna(s) referente(s) a outra entidade na tabela de relacionamento
	 * @return string
	 */
	public function getInverseJoinColumns() {
		if ($this->hasAnnotation("JoinTable")) {
			return $this->getAnnotation("JoinTable")->getInverseJoinColumns();
		} else {
			die("[ORM] Sem defaults para JoinTable. Declarar @JoinTable na propriedade [".$this->getDeclaringClass()->getName().".".$this->getName()."]");
		}
	}

	/** Retorna o tipo do dado da propriedade
	 * @return string
	 */
	public function getType() {
		return $this->hasAnnotation('var') ? $this->getAnnotation('var') : 'string';
	}

	/**
	 * @return boolean
	 */
	public function hasAnnotation($name) {
		if (parent::hasAnnotation($name)) return true;
		if ($this->rflctGetter && $this->rflctGetter->hasAnnotation($name)) return true;
		if ($this->rflctSetter && $this->rflctSetter->hasAnnotation($name)) return true;
		return false;
	}

	/**
	 * @return Annotation
	 */
	public function getAnnotation($name) {
		if (parent::hasAnnotation($name)) return parent::getAnnotation($name);
		if ($this->rflctGetter && $this->rflctGetter->hasAnnotation($name)) return $this->rflctGetter->getAnnotation($name);
		if ($this->rflctSetter && $this->rflctSetter->hasAnnotation($name)) return $this->rflctSetter->getAnnotation($name);
		throw new ReflectionException("[ORM] Anotacao [".$name."] nao encontrada na propriedade [".$this->getDeclaringClass()->getName().".".$this->getName()."]");
	}

	/**
	 * @return ColumnAnnotation
	 */
	public function getColumn() {
		if ($this->isColumn() || ($this->isJoined() && !$this->getMappedBy())) {
			if ($this->hasAnnotation("Column")) return $this->getAnnotation("Column");
			return new ColumnAnnotation($this->getName());
		} else {
			throw new ReflectionException("A propriedade [".$this->getDeclaringClass()->getName().".".$this->getName()."] nao esta anotada como coluna");
		}
	}

	/**
	 * @return AbstractRelationshipAnnotation
	 */
	private function getAssociation() {
		if ($this->isJoined()) {
			if ($this->hasAnnotation("OneToOne")) return $this->getAnnotation("OneToOne");
			if ($this->hasAnnotation("ManyToOne")) return $this->getAnnotation("ManyToOne");
		} else if ($this->isCollection()) {
			if ($this->hasAnnotation("OneToMany")) return $this->getAnnotation("OneToMany");
			if ($this->hasAnnotation("ManyToMany")) return $this->getAnnotation("ManyToMany");
		} else {
			throw new ReflectionException("A propriedade [".$this->getDeclaringClass()->getName().".".$this->getName()."] nao esta anotada como relacionamento");
		}
	}

	/**
	 * @return ManyToManyAnnotation
	 */
	private function getManyToManyAssociation() {
		if ($this->hasAnnotation("ManyToMany")) {
			return $this->getAnnotation("ManyToMany");
		} else {
			throw new ReflectionException("A propriedade [".$this->getDeclaringClass()->getName().".".$this->getName()."] nao possui anotacao @ManyToMany");
		}
	}

	/** Retorna a estrategia de geracao do indice
	 * @return string
	 */
	public function getGenerationStrategy() {
		if ($this->isIndex()) {
			return $this->getAnnotation("Id")->getStrategy();
		}
	}

	/** Verifica se a propriedade é um relacionamento One to One
	 * @return boolean
	 */
	public function isOneToOne() {
		return $this->hasAnnotation("OneToOne") ? true : false;
	}

	/** Verifica se a propriedade é um relacionamento Many to One
	 * @return boolean
	 */
	public function isManyToOne() {
		return $this->hasAnnotation("ManyToOne") ? true : false;
	}

	/** Verifica se a propriedade é um relacionamento One to Many
	 * @return boolean
	 */
	public function isOneToMany() {
		return $this->hasAnnotation("OneToMany") ? true : false;
	}

	/** Verifica se a propriedade é um relacionamento Many to Many
	 * @return boolean
	 */
	public function isManyToMany() {
		return $this->hasAnnotation("ManyToMany") ? true : false;
	}

	/** Retorna a classe ORM declarante
	 * @return ReflectionORMClass
	 */
	public function getDeclaringORMClass() {
		return $this->declaringORMClass;
	}

	/** Retorna as colunas componentes do mapeamento
	 * @return array
	 */
	public function getCompositeColumns() {
		if ($this->isComposite()) {
			return $this->getAnnotation("CompositeColumn")->getColumns();
		}
	}

	/** Verifica se a propriedade e mapeada em mais de uma coluna
	 * @return boolean
	 */
	public function isComposite() {
		return $this->hasAnnotation("CompositeColumn") ? true : false;
	}

	public function isForeignKey() {
		return (isset($this->parentProperty)||isset($this->foreignIndexORMProperties)) ? true : false;
	}

	/**
	 * @return array
	 */
	public function getForeignIndexORMProperties() {
		return $this->foreignIndexORMProperties;
	}

	/**
	 * @param array $newForeignIndexORMProperties
	 * @return void
	 */
	private function __setForeignIndexORMProperties($newForeignIndexORMProperties) {
		$this->foreignIndexORMProperties = $newForeignIndexORMProperties;
	}

	/** Retorna o valor da propriedade no objeto fornecido
	 * @param object $entity    Objeto no qual obter o valor
	 * @param boolean $initialize    Cria as entidades referenciadas
	 * @return mixed
	 */
	public function getValue($object = null) {
		
		//Hack para tornar a assinatura do metodo compativel com a definicao
		if (func_num_args() == 2) {
			$initialize = func_get_arg(1);
		} else {
			$initialize = false;
		}
		
		if ($this->parentProperty) {
			$parentValue = $this->parentProperty->getValue($object);
			if (is_null($parentValue)) {
				if ($initialize) {
					if (class_exists($this->parentProperty->getType())  && in_array( $this->parentProperty->getType(), get_declared_classes() )) {
						$className = $this->parentProperty->getType();
						$this->parentProperty->setValue($object,new $className());
						$parentValue = $this->parentProperty->getValue($object);
					}
				} else {
					throw new ORMException("O caminho para a propriedade apontada nao foi encontrado.");
				}
			}
			return $this->rflctGetter ? $this->rflctGetter->invoke( $parentValue ) : $parentValue->{$this->getName()};
		} else {
			return $this->rflctGetter ? $this->rflctGetter->invoke($object) : $object->{$this->getName()};
		}
	}

	/** Define o valor da propriedade no objeto fornecido
	 * @param object $entity    Objeto no qual definir o valor
	 * @param mixed $value    Valor a ser definido
	 * @param boolean $initialize    Cria as entidades referenciadas
	 * @return void
	 */
	public function setValue($entity, $value) {
		
		//Hack para tornar a assinatura do metodo compativel com a definicao
		if (func_num_args() == 3) {
			$initialize = func_get_arg(2);
		} else {
			$initialize = false;
		}
		
		if ($this->parentProperty) {
			$parentValue = $this->parentProperty->getValue($entity);
			if (is_null($parentValue)) {
				if ($initialize) {
					if (class_exists($this->parentProperty->getType())  && in_array( $this->parentProperty->getType(), get_declared_classes() )) {
						$className = $this->parentProperty->getType();
						$this->parentProperty->setValue($entity,new $className());
						$parentValue = $this->parentProperty->getValue($entity);
					}
				} else {
					throw new ORMException("O caminho para a propriedade apontada nao foi encontrado.");
				}
			}
			return $this->rflctSetter ? $this->rflctSetter->invoke($parentValue,$value) : $parentValue->{$this->getName()} = $value;
		} else {
			return $this->rflctSetter ? $this->rflctSetter->invoke($entity,$value) : $entity->{$this->getName()} = $value;
		}
	}

	private function __overrideAnnotation($name,Annotation $value) {
		$this->annotations[$name] = $value;
	}

	public function __call($name,$arguments) {
		if (($name == "__overrideAnnotation")||
		($name == "__setForeignIndexORMProperties")) {
			call_user_func_array(array($this,$name),$arguments);
		} else {
			throw new ReflectionException("[FATAL] Metodo nao encontrado ".$name);
		}
	}
}

?>