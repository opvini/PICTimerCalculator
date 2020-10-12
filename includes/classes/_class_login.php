<?php

//**************************************
//
// Criado por: Vinícius Nunes Lage
// Criada em: 07/05/2014
//
//**************************************


require_once("_class_conexao.php");
require_once("_class_tratamento.php");


class Login{

	private $user;
	private $pass;
	private $data;
	
	private $con;	// conexão com o banco
	private $trt;	// tratamento
	
	function __construct()
	{
		$this->trt = new Tratamento;
		$this->con = new Conexao;
	}
	
	private function trata($user, $pass)
	{
		$this->user = $this->trt->BD($this->user);
		$this->pass = $this->trt->BD($this->pass);
	}
	
	public function entra($user, $pass)
	{
		$this->trata($user, $pass);	
		$this->con->conecta();
		
		$this->con->query("SELECT usuario, senha 
						   FROM clientes
						   WHERE usuario = '".$this->user."'
						   AND	 senha	 = '".$this->pass."';
						  ");
		
	}

}


?>