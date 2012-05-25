<?php
class Categoria {
    // atributos
	var $idCategoria;
	var $nome;

                       
    // Getters e Setters
	function setIdCategoria($idCategoria) {
	   $this->idCategoria = $idCategoria;
	}
	
	function getIdCategoria() {
	    return $this->idCategoria;
	}
	
	function setNome($nome) {
	   $this->nome = $nome;
	}
	
	function getNome() {
	    return $this->nome;
	}
	
}
?>