<?php
class Cliente {
    // atributos

	private $idCliente;
    private $nome;
    private $sobrenome;
    private $cpf;
    private $dt_nasc;
    private $sexo;
    private $email;
    private $login;
    private $senha;
    private $cep;
    private $endereco;
    private $numero;
    private $complemento;
    private $bairro;
    private $cidade;
    private $estado;
    private $telefone_1;
    private $telefone_2;
    private $referencia;
                       
    // Getters e Setters
	public function setIdCliente($idCliente) {
	   $this->idCliente = $idCliente;
	}
	
	public function getIdCliente() {
	    return $this->idCliente;
	}
	
	public function setNome($nome) {
	   $this->nome = $nome;
	}
	
	public function getNome() {
	    return $this->nome;
	}
	
	public function setSobrenome($sobrenome) {
	   $this->sobrenome = $sobrenome;
	}
	
	public function getSobrenome() {
	    return $this->sobrenome;
	}
	
	public function setCpf($cpf) {
	   $this->cpf = $cpf;
	}
	
	public function getCpf() {
	    return $this->cpf;
	}
	
	public function setDt_nasc($dt_nasc) {
	   $this->dt_nasc = $dt_nasc;
	}
	
	public function getDt_nasc() {
	    return $this->dt_nasc;
	}
	
	public function setSexo($sexo) {
	   $this->sexo = $sexo;
	}
	
	public function getSexo() {
	    return $this->sexo;
	}
	
	public function setEmail($email) {
	   $this->email = $email;
	}
	
	public function getEmail() {
	    return $this->email;
	}
	
	public function setLogin($login) {
	   $this->login = $login;
	}
	
	public function getLogin() {
	    return $this->login;
	}
	
	public function setSenha($senha) {
	   $this->senha = $senha;
	}
	
	public function getSenha() {
	    return $this->senha;
	}
	
	public function setCep($cep) {
	   $this->cep = $cep;
	}
	
	public function getCep() {
	    return $this->cep;
	}
	
	public function setEndereco($endereco) {
	   $this->endereco = $endereco;
	}
	
	public function getEndereco() {
	    return $this->endereco;
	}
	
	public function setNumero($numero) {
	   $this->numero = $numero;
	}
	
	public function getNumero() {
	    return $this->numero;
	}
	
	public function setComplemento($complemento) {
	   $this->complemento = $complemento;
	}
	
	public function getComplemento() {
	    return $this->complemento;
	}
	
	public function setBairro($bairro) {
	   $this->bairro = $bairro;
	}
	
	public function getBairro() {
	    return $this->bairro;
	}
	
	public function setCidade($cidade) {
	   $this->cidade = $cidade;
	}
	
	public function getCidade() {
	    return $this->cidade;
	}
	
	public function setEstado($estado) {
	   $this->estado = $estado;
	}
	
	public function getEstado() {
	    return $this->estado;
	}
	
	public function setTelefone_1($telefone_1) {
	   $this->telefone_1 = $telefone_1;
	}
	
	public function getTelefone_1() {
	    return $this->telefone_1;
	}
	
	public function setTelefone_2($telefone_2) {
	   $this->telefone_2 = $telefone_2;
	}
	
	public function getTelefone_2() {
	    return $this->telefone_2;
	}
	
	public function setReferencia($referencia) {
	   $this->referencia = $referencia;
	}
	
	public function getReferencia() {
	    return $this->referencia;
	}

    
    
}
?>