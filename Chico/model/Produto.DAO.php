<?php
// incluindo conexуo com o banco
require_once ("../model/database/conn.php");

// incluindo a classe Produto
require_once ("../model/Produto.class.php");
 
class ProdutoDAO extends Produto {
    
	// Variavel de conexao com o banco
	var $bd;
	
	function ProdutoDAO() {
		$bd = new ConexaoBD();
	}

                       
    // Metodo de Inclusуo
	public function IncluirProduto() {
	   	$sql = "insert into produto (nome, descricao, valor, categoria_idcategoria) values ( '".$this->getNome()."', ".$this->getDescricao()." , ".$this->getValor()." , '".$this->getCategoria_idCategoria()."' ";
		$result = mysql_query( $sql );
		
		if( !$result )
		{
		    return false;
		}
		
		return true;
	}
	
	// Metodo de Alteraчуo
	function AlterarProduto()
	{
		$sql = "update produto set nome = '".$this->getNome()."', descricao = '".$this->getDescricao()."', valor = '".$this->getValor()."', categoria_idcategoria = '".$this->getCategoria_idCategoria()."' where idProduto = ".$this->getIdProduto()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}	
	
	// Metodo de Exclusуo
	function DeletarProduto()
	{
		$sql = "delete from produto where idProduto = ".$this->getIdProduto()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}
	
	// Metodo de Consulta
	function ConsultaProduto()
	{
		$sql = "select idProduto, nome, descricao, valor, categoria_idcategoria from produto where 1=1 ";
	
		$result = mysql_query( $sql );
	
		return $result;
	}
	
	// Metodo de Consulta por categoria
	function ConsultaProdutoPorCategoria()
	{
		$sql = "select idProduto, nome, descricao, valor, categoria_idcategoria from produto where categoria_idcategoria = ".$this->getCategoria_idCategoria()." ";
	
		$result = mysql_query( $sql );
	
		return $result;
	}
	
	// Metodo de Consulta por produto true ou false
	function ConsultaProdutoPorProduto($prod)
	{
		$sql = "select idProduto, nome, descricao, valor,categoria_idCategoria from produto where idProduto = ".$prod." ";
		$result = mysql_query( $sql );
		return $result;
	}
}

$ProdutoDAO = new ProdutoDAO();
?>