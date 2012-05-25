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
 * File: ORMDeleterBuilder.php
 **/

import('engine.db.ORMBuilder');

/** Constroi uma operacao de exclusao da entidade
 * @author Silas R. N. Junior
 */
class ORMDeleterBuilder extends ORMBuilder {

	/** Constroi o proximo mapeamento
	 * @param string $className    Nome da classe
	 * @param array $path    Caminho desde o objeto raiz
	 * @param string $ownerClassProperty    Propriedade da classe conteiner que referencia esta classe
	 * @param ORM $ownerClassORM    Mapeamento da classe conteiner
	 * @param int $depth    Contador de Profundidade de recursao
	 * @return ORM
	 */
	public function build($className, $path = null, $ownerClassProperty = null, ORM &$ownerClassORM = null, $depth = null) {

		$enqueue = true;
		
		//Classe de reflexao
		$reflectionClass = EntityManager::getReflectionData($className);
		
		//Testa o depth para referencias ciclicas
		if ($this->isRecursion($path,$className)) {
			if (!isset($depth)) throw new Exception("[ORM] Referencias ciclicas exigem que o [depth] esteja setado. Ex: @OneToOne(depth=1) [path:{$path}]");
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
		
		//Deleter
		$ORM = new ORMDeleter($reflectionClass,ORMBuilder::getNextAlias(),$strPath,$depth);
		
		//Processa arvore
		$deleteAfter = array();
		foreach ($reflectionClass->getJoins() as $join) {
			//Mapeamentos bidirecionais
			if ($join->getMappedBy()) { //Referenciado
				if ($join->getMappedBy() == $ownerClassProperty && ($join->getType() == $ownerClassORM->getRflctORM()->getName())) {
					continue;
				} else {
					if (!$join->getCascade() || ($join->getCascade() != CascadeType::NONE && $join->getCascade() != CascadeType::DELETE) ) {
						$nextPath = $path;
						$nextPath[$strPath.".".$join->getName()] = $join->getType();
						$joinORM = $this->build($join->getType(),$nextPath,$join->getName(),$ORM,!is_null($depth) ? $depth : $join->getDepth());
						if (!is_null($joinORM)) $joinORM->setCascade($join->getCascade());
					}
				}
			} else { //Referenciador
				//Autorelacionamento
				if ($join->isManyToOne() && $join->getType() == $className && !$ownerClassORM) {
					$enqueue = false;
				}
				if ($ownerClassORM && $ownerClassORM->getRflctORM()->isMapping($reflectionClass->getName(),$join->getName())) {
					continue;
				} else {
					if (!$join->getCascade() || (($join->getCascade() == CascadeType::DELETE)||($join->getCascade() == CascadeType::ALL)) ) $deleteAfter[] = $join;
				}
			}
		}
		
		//Root & Next
		if ($enqueue) {
			if (!$this->getRoot()) {
				$this->setRoot($ORM);
			} else {
				$this->getRoot()->setNext($ORM);
			}
		}
		
		//Deletar mapeamentos dependentes
		foreach ($deleteAfter as $join) {
			$nextPath = $path;
			$nextPath[$strPath.".".$join->getName()] = $join->getType();
			$joinORM = $this->build($join->getType(),$nextPath,$join->getName(),$ORM,!is_null($depth) ? $depth : $join->getDepth());
			if (!is_null($joinORM)) $joinORM->setCascade($join->getCascade());
		}
		
		//Heranca
		if ($reflectionClass->getParentClass() && $reflectionClass->getInheritanceStrategy() == InheritanceType::TABLE_PER_CLASS) {
			$null = null;
			$parentORM = $this->build($reflectionClass->getParentClass()->getName(),$path,null,$null,$depth);
		}
		return $ORM;
	}
}

?>
