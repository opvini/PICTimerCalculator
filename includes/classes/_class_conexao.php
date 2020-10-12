<?php

//**************************************
//
// Criado por: Vinícius Nunes Lage
// Criada em: 07/05/2014
//
//**************************************



//////////////////////////////////////////// DADOS CONSTANTES INICIAIS DO BANCO

define(	"host_"	, 	"localhost"	);
define( "user_"	, 	"root"		);
define( "pass_"	, 	"localhost"	);
define( "bd_"	, 	"test"		);

//////////////////////////////////////////// 



class Conexao{
	
	public $link;
	
	private $host;
	private $user;
	private $pass;
	private $bd;
	
	private $conectado	= false;
	private $error		= "";
	private $tot_rows	= 0;
	private $resultado	= "";			// resultado completo das querys


	function __construct($host=host_, $user=user_, $pass=pass_, $bd=bd_)
	{
		//$this->conecta($host, $user, $pass, $bd);
	}
	
	
	private function inicializa($host, $user, $pass, $bd)
	{
		$this->host  = $host;
		$this->user  = $user;
		$this->pass  = $pass;
		$this->bd	 = $bd;
	}
	

	public function conecta($host=host_, $user=user_, $pass=pass_, $bd=bd_)
	{
		$this->inicializa($host, $user, $pass, $bd);
		$this->link = new mysqli( $this->host , $this->user, $this->pass, $this->bd );
		
		if(!$this->link->connect_error) 
			$this->conectado = true;
		else{
			$this->conectado = false;
			$this->error = $this->link->error;
		}
	}
		
	public function desconecta()
	{
		if($this->conectado) @$this->link->close;
	}
	
	
	public function query($query)
	{
		if( $this->conectado && ($this->resultado = $this->link->query($query)) ){
				$this->tot_rows = $this->resultado->num_rows;
		}
		else
		{
			$this->error = $this->link->error; 
			return false;
		}
	}
	

	public function simple_query($query)
	{
		if( $this->conectado && ($this->resultado = $this->link->query($query)) ){
			return mysqli_insert_id($this->link);
		}
		else
		{
			$this->error = $this->link->error;  
			return false;
		}
	}
	
	
	
	//////////////////////////////////////// GET and SETTERS
	
	public function is_conectado(){
		return $this->conectado;
	}
	
	public function get_tot_rows(){
		return $this->tot_rows;
	}
	
	public function get_errors(){
		return $this->error;
	}
	
	public function get_row(){
		@$this->result = mysqli_fetch_object($this->resultado);
		return $this->result;
	}
	

}

?>