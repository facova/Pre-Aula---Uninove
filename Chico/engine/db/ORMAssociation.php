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
 * File: ORMAssociation.php
 **/


/**
 * @author Silas R. N. Junior
 */
class ORMAssociation {

	/**
	 * @var ORM
	 */
	private $ownerORM;

	/**
	 * @var ORM
	 */
	private $referencedORM;

	/**
	 * @var ReflectionORMProperty
	 */
	private $ownerProperty;

	/** Tipo do JOIN
	 * @var string
	 */
	private $type;

	/**
	 * @param ORM $ownerORM
	 * @param ORM $referencedORM
	 * @param ReflectionORMProperty $property
	 */
	public function ORMAssociation(ORM $ownerORM, ORM $referencedORM, $property,$type = JoinType::LEFT_JOIN) {
		$this->ownerORM = $ownerORM;
		$this->referencedORM = $referencedORM;
		$this->ownerProperty = $property;
		$this->type = $type;
	}

	/** Retorna a string SQL formatada
	 * @param DbDriver $driver    Driver do banco
	 * @return string
	 */
	public function getSQLString(DbDriver $driver) {
		$sql = $this->type." ".$driver->formatTable($this->referencedORM->getRflctORM()->getTableName())." ".$this->referencedORM->getAlias()." ON ";
		
		//Heranca
		if ($this->ownerProperty == "super") {
			//Recupera indices
			$parent = $this->referencedORM->getRflctORM()->getParentORMClass();
			while ($parent->getParentClass()) {
				$parent = $parent->getParentORMClass();
			}
			$parentIndexes = $parent->getIndexes();
			//verifica overrides
			if ($this->referencedORM->getRflctORM()->hasAnnotation("JoinColumn")) {
				$referecedColumnName = $this->referencedORM->getRflctORM()->getAnnotation("JoinColumn")->getName();
			} else {
				$referecedColumnName = current($parentIndexes)->getColumnName();
			}
			
			if ($this->ownerORM->getRflctORM()->hasAnnotation("JoinColumn")) {
				$ownerColumnName = $this->ownerORM->getRflctORM()->getAnnotation("JoinColumn")->getName();
			} else {
				$ownerColumnName = current($parentIndexes)->getColumnName();
			}
			return $sql . $this->referencedORM->getAlias().".".$driver->formatField( $referecedColumnName )." = ".$this->ownerORM->getAlias().".".$driver->formatField( $ownerColumnName );
			
		}
		
		//Chave estrangeira
		if ($this->ownerProperty->isForeignKey()) {
			//Composta
			if ($this->ownerProperty->isComposite()) {
				
				$ownerIndexes = $this->ownerProperty->getForeignIndexORMProperties();
				$referIndexProperties = $this->referencedORM->getRflctORM()->getIndexes();
				$referIndexes = array();
				foreach ($referIndexProperties as $referIndex) {
					if ($referIndex->isComposite()) {
						$referIndexes = array_merge($referIndexes,$referIndex->getForeignIndexORMProperties());
					} else {
						$referIndexes[] = $referIndex;
					}
				}
				//Erro de numero de indices
				if (sizeof($ownerIndexes) != sizeof($referIndexes)) throw new ORMException("O numero de colunas do indice mapeado na propriedade ".$this->ownerORM->getRflctORM()->getName().".".$this->ownerProperty->getName()." nao corresponde ao numero de indices da entidade referenciadada [".$this->ownerProperty->getType()."]");
				
			//Simples
			} else {
				$ownerIndexes = array($this->ownerProperty);
				$referIndexes = $this->referencedORM->getRflctORM()->getIndexes();
			}
			$joins = array();
			for ($i = 0; $i < sizeof($ownerIndexes); $i++) {
				$ownerColumnName = $ownerIndexes[$i]->getColumnName();
				$referecedColumnName = $referIndexes[$i]->getColumnName();
				$joins[] = $this->ownerORM->getAlias().".".$driver->formatField( $ownerColumnName )." = ".$this->referencedORM->getAlias().".".$driver->formatField( $referecedColumnName );
			}
			return $sql ."(".implode(" AND ",$joins).")";
		}
		
		//Mapeamentos compostos
		if ($this->ownerProperty->hasAnnotation("Composite")) {
			
		}

		if ($this->ownerProperty->getMappedBy()) {
			if ($this->ownerProperty->getMappedBy() instanceof ColumnAnnotation) {
				$mappedColumn = $this->ownerProperty->getMappedBy()->getName();
			} else {
				$mappedColumn = $this->referencedORM->getRflctORM()->getORMProperty($this->ownerProperty->getMappedBy())->getColumnName();
			}
				
			$sql .= $this->ownerORM->getAlias().".".$driver->formatField( current($this->ownerORM->getRflctORM()->getIndexes())->getColumnName() )." = ".$this->referencedORM->getAlias().".".$driver->formatField($mappedColumn);
		} else {
			$sql .= $this->ownerORM->getAlias().".".$driver->formatField( $this->ownerProperty->getColumnName() )." = ".$this->referencedORM->getAlias().".".$driver->formatField( current($this->referencedORM->getRflctORM()->getIndexes())->getColumnName() );
		}

		return $sql;
	}
}

?>
