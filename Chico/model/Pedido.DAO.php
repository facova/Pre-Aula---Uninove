<?php
// incluindo conexуo com o banco
include_once ("../model/database/conn.php");

// incluindo a classe Pedido
include_once ("../model/Pedido.class.php");
 
class PedidoDAO extends Pedido {
    
	// Variavel de conexao com o banco
	var $bd;
	
	function PedidoDAO() {
		$bd = new ConectaBD();
	}

                       
    // Metodo de Inclusуo
	public function IncluirPedido() {
	   	$sql = "insert into pedido (data, valor_pedido, valor_pago, troco, Cliente_idCliente, Produto_idProduto, Status_idStatus) values ( '".$this->getData()."', '".$this->getValor_pedido()."', '".$this->getValor_pago()."', '".$this->getTroco()."', '".$this->getCliente_idCliente()."', '".$this->getProduto_idProduto()."', '".$this->getStatus_idStatus()."')";
		$result = mysql_query( $sql );
		
		if( !$result )
		{
		    return false;
		}
		
		return true;
	}
	
	
	// Metodo de Inclusуo de Produtos do Pedido
	public function IncluirProdutoPedido() {
		$sql = "insert into pedido_itens (pedido_idpedido, produto_idproduto, quantidade) values ( '".$this->getPedido_idPedido()."', '".$this->getProduto_idProduto()."', '".$this->getQuantidade()."')";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}
	
	
	// Metodo de Alteraчуo
	function AlterarPedido()
	{
		$sql = "update pedido set nome = '".$this->getNome()."' where idPedido = ".$this->getIdPedido()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}	
	
	// Metodo de Exclusуo
	function DeletarPedido()
	{
		$sql = "delete from pedido where idPedido = ".$this->getIdPedido()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}
	
	// Metodo de Consulta
	function ConsultaPedido()
	{
		$sql = "select idPedido,nome from pedido where 1=1 ";
	
		if( $this->getNome() != "" )
			$sql = $sql . " and nome like '%".$this->getNome()."%' ";
			
		if( $this->getIdPedido() != "" )
			$sql = $sql . " and idPedido like '%".$this->getIdPedido()."%' ";		
	
		$result = mysql_query( $sql );
	
		return $result;
	}
	
}

$PedidoDAO = new PedidoDAO();
?>