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
 * File: ORMLoadBuilder.php
 **/

import('engine.db.ORMBuilder');

/** Constroi uma operacao de carregamento da entidade
 * @author Silas R. N. Junior
 */
class ORMLoaderBuilder extends ORMBuilder {

	/**
	 * @param int $alias    Contagem de alias inicial
	 */
	public function ORMLoaderBuilder($reset = false) {
		if ($reset) {
			ORMBuilder::resetCounter();
		}
	}

	/** Constroi o proximo mapeamento
	 * @param string $className   Nome da classe
	 * @param array $path   Caminho desde o objeto raiz
	 * @param string $ownerClassProperty   Propriedade da classe conteiner que referencia esta classe
	 * @param ORM $ownerClassORM   Mapeamento da classe conteiner
	 * @param int $depth   Contador de Profundidade de recursao
	 * @return ORMLoader
	 */
	public function build($className, $path = null, $ownerClassProperty = null, ORM &$ownerClassORM = null, $depth = null) {

		//Classe de reflexao
		$reflectionClass = EntityManager::getReflectionData($className);
		
		//Testa o depth para referencias ciclicas
		if ($this->isRecursion($path,$className)) {
			if (!isset($depth)) throw new Exception("[ORM] Erro nas associacoes. Uma referencia ciclica foi detectada sem o depth setado. Ex: @OneToOne(depth=1) [path:{$path}]");
			$depth--;
			if ($depth < 0) return null;
		}
		
		//Prepara o path
		if($path == null) {
			$strPath = "root#";
			$path = array($strPath => $className);
		} else {
			end($path);
			$strPath = key($path);
		}

		//Loader
		$ORM = new ORMLoader($reflectionClass,ORMBuilder::getNextAlias(),$strPath,$depth);
		
		//Heranca
		if ($reflectionClass->getParentClass() && $reflectionClass->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
			//ParentORM se liga com a entidade referenciadora
			$parentORM = $this->build($reflectionClass->getParentClass()->getName(),$path,$ownerClassProperty,$ownerClassORM,$depth);
		}
		
		//Encadeamento
		if ($ownerClassORM) { //Entidade referenciada
			if (isset($parentORM)) { //Subclasse de Heranca
				//LAZY OU FETCH?
				if (!isset($ownerClassProperty) || !$ownerClassProperty->getFetch() || $ownerClassProperty->getFetch() == FetchType::FETCH) {
					//Caso o root seja diferente do parent
					/*
					if ($this->getRoot() && spl_object_hash($this->getRoot()) != spl_object_hash($parentORM)) {
						$this->getRoot()->setNext($parentORM);
					}
					*/
					$this->getRoot()->setNext($ORM);
					$ORM->addAssociation(new ORMAssociation($parentORM,$ORM,"super",JoinType::INNER_JOIN));
				} else if ($ownerClassProperty->getFetch() == FetchType::LAZY) {
					//$ownerClassORM->addLazyLoad($ORM);
				}
				
			} else {
				//LAZY OU FETCH?
				if (!isset($ownerClassProperty) || !$ownerClassProperty->getFetch() || $ownerClassProperty->getFetch() == FetchType::FETCH) {

					//Caso nao exista o root
					if (!$this->getRoot()) {
						$this->setRoot($ORM);
					} else {
						$this->getRoot()->setNext($ORM);
					}
				} else if ($ownerClassProperty->getFetch() == FetchType::LAZY) {
					$ownerClassORM->addLazyLoad($ORM);
				}
				
				//Associa com a entidade anterior se nao for parte de uma chave estrangeira
				if (!$ownerClassProperty->isForeignKey()) {
					$ownerClassORM->addAssociation(new ORMAssociation($ownerClassORM,$ORM,$ownerClassProperty));
				}
			}
		} else { //Entidade normal
			//Caso nao exista o root
			if (!$this->getRoot()) {
				$this->setRoot($ORM);
			} else {
				$this->getRoot()->setNext($ORM);
			}
			if (isset($parentORM)) { //Subclasse de Heranca
					$ORM->addAssociation(new ORMAssociation($parentORM,$ORM,"super",JoinType::INNER_JOIN));
			}
		}

		//Indices Chave Estrangeira
		foreach ($reflectionClass->getIndexes() as $index) {
			if ($index->isForeignKey()) {
				$nextPath = $path;
				$nextPath[$strPath.".".$index->getName()] = $index->getType();
				$indexORM = $this->build($index->getType(),$nextPath,$index,$ORM,!is_null($depth) ? $depth : $index->getDepth());
				if(!is_null($indexORM)) {
					$ORM->addAssociation(new ORMAssociation($ORM,$indexORM,$index,JoinType::INNER_JOIN));
				}
			}
		}

		//Processa arvore se anterior for fetch
		if (!isset($ownerClassProperty) || !$ownerClassProperty->getFetch() || $ownerClassProperty->getFetch() == FetchType::FETCH) {
			foreach ($reflectionClass->getJoins() as $join) {
				if ($join->isIndex()) continue; //Indices ja foram tratados
				//Mapeamentos bidirecionais
				if ($join->getMappedBy()) {
					if ($ownerClassProperty && $join->getMappedBy() == $ownerClassProperty->getName()) {
						if ($join->getType() == $ownerClassORM->getRflctORM()->getName()) continue;
					}
				} else {
					if ($ownerClassORM && $ownerClassORM->getRflctORM()->isMapping($reflectionClass->getName(),$join->getName())) continue;
				}
				//Proximo nodo
				$nextPath = $path;
				$nextPath[$strPath.".".$join->getName()] = $join->getType();
				$this->build($join->getType(),$nextPath,$join,$ORM,!is_null($depth) ? $depth : $join->getDepth());
			}
		}
		
		return $ORM;
	}
}
?>
