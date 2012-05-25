<?php
class Produto {
    // atributos
	private $idProduto;
	private $nome;
	private $descricao;
	private $valor;
	private $Categoria_idCategoria;

                       
    // Getters e Setters
	public function setIdProduto($idProduto) {
	   $this->idProduto = $idProduto;
	}
	
	public function getIdProduto() {
	    return $this->idProduto;
	}
	
	public function setNome($nome) {
	   $this->nome = $nome;
	}
	
	public function getNome() {
	    return $this->nome;
	}
	
	public function setDescricao($descricao) {
	   $this->descricao = $descricao;
	}
	
	public function getDescricao() {
	    return $this->descricao;
	}
	
	public function setValor($valor) {
	   $this->valor = $valor;
	}
	
	public function getValor() {
	    return $this->valor;
	}
	
	public function setCategoria_idCategoria($Categoria_idCategoria) {
	   $this->Categoria_idCategoria = $Categoria_idCategoria;
	}
	
	public function getCategoria_idCategoria() {
	    return $this->Categoria_idCategoria;
	}
}
?>