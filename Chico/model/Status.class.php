<?php
class Status {
    // atributos
	private $idStatus;
	private $descricao;

                       
    // Getters e Setters
	public function setIdStatus($idStatus) {
	   $this->idStatus = $idStatus;
	}
	
	public function getIdStatus() {
	    return $this->$idStatus;
	}
	
	public function setDescricao($descricao) {
	   $this->descricao = $descricao;
	}
	
	public function getDescricao() {
	    return $this->$descricao;
	}
	
}
?>