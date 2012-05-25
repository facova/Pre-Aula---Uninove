<?php
class Pedido {
    // atributos
	private $idPedido;
	private $data;
	private $valor_pedido;
	private $valor_pago;
	private $troco;
	private $quantidade;
	private $Cliente_idCliente;
	private $Status_idStatus;
	private $Pedido_idPedido;
	private $Produto_idProduto;
	
                       
    // Getters e Setters
public function setIdPedido($idPedido) {
   $this->idPedido = $idPedido;
}

public function getIdPedido() {
    return $this->idPedido;
}

public function setData($data) {
   $this->data = $data;
}

public function getData() {
    return $this->data;
}

public function setValor_pedido($valor_pedido) {
   $this->valor_pedido = $valor_pedido;
}

public function getValor_pedido() {
    return $this->valor_pedido;
}

public function setValor_pago($valor_pago) {
   $this->valor_pago = $valor_pago;
}

public function getValor_pago() {
    return $this->valor_pago;
}

public function setTroco($troco) {
   $this->troco = $troco;
}

public function getTroco() {
    return $this->troco;
}

public function setQuantidade($quantidade) {
   $this->quantidade = $quantidade;
}

public function getQuantidade() {
    return $this->quantidade;
}

public function setCliente_idCliente($Cliente_idCliente) {
   $this->Cliente_idCliente = $Cliente_idCliente;
}

public function getCliente_idCliente() {
    return $this->Cliente_idCliente;
}

public function setStatus_idStatus($Status_idStatus) {
   $this->Status_idStatus = $Status_idStatus;
}

public function getStatus_idStatus() {
    return $this->Status_idStatus;
}

public function setProduto_idProduto($Produto_idProduto) {
	$this->Produto_idProduto = $Produto_idProduto;
}

public function getProduto_idProduto() {
	return $this->Produto_idProduto;
}

public function setc($Pedido_idPedido) {
	$this->Pedido_idPedido = $Pedido_idPedido;
}

public function getPedido_idPedido() {
	return $this->Pedido_idPedido;
}	
	
}
?>