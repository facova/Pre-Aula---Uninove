<?php
// incluindo conexão com o banco
include_once ("../model/database/conn.php");

// incluindo a classe Status
include_once ("../model/Categoria.class.php");
 
class CategoriaDAO extends Categoria {
	
	// Variavel de conexao com o banco
	var $bd;
	
	function CategoriaDAO()	{
		$bd = new ConexaoBD();
	}

	
    // Metodo de Inclusão
	public function IncluirCategoria() {
	   	$sql = "insert into categoria (nome) values ('". $this->getNome() ."')";
		$result = mysql_query( $sql );
		
		if( !$result )
		{
		    return false;
		}
		
		return true;
	}
	
	// Metodo de Alteração
	function AlterarCategoria()
	{
		$sql = "update categoria set nome = '".$this->getNome()."' where idCategoria = ".$this->getIdCategoria()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}	
	
	// Metodo de Exclusão
	function DeletarCategoria()
	{
		$sql = "delete from categoria where idCategoria = ".$this->getIdCategoria()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}
	
	// Metodo de Consulta
	function ConsultaCategoria()
	{
		$sql = "select idCategoria,nome from categoria where 1=1 ";
		$result = mysql_query( $sql );
	
		return $result;
	}
	
	// Metodo de Consulta Unica
	function ConsultaCategoriaUnica()
	{
		$sql = "select nome from categoria where idCategoria = ".$this->getIdCategoria()."";
		$result = mysql_query( $sql );
	
		return $result;
	}

}

$CategoriaDAO = new CategoriaDAO();

?>
