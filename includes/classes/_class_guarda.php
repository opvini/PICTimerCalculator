<?php

@session_start();
include_once( "_class_conexao.php" );

class Guarda{
	
	private $conexao;
	
	/*
	private $con_host  = "db1.isecretaria.net";
	private $con_user  = "op_vini";
	private $con_senha = 'H#Lo^87$S';
	private $con_bd    = "guarda";
	/**/
	
	//*
	private $con_host  = "localhost";
	private $con_user  = "root";
	private $con_senha = "";
	private $con_bd    = "guarda_teste";
	
	/**/
	
	private $con_result;
	private $id_login  			= 0;
	private $user_privileges  	= "";
	private $tot_pages 	 		= 0;
	private $tot_results 		= 0;
	private $tot_page 	 		= 0;
	
	public function __construct(){
		$this->conexao = new Conexao;
		$this->conexao->conecta( $this->con_host, $this->con_user, $this->con_senha, $this->con_bd);
	}
	
	
	public function is_adm(){
		if( $this->user_privileges == "1" ) return true;
		else return false;
	}

	public function get_privileges(){
		return $this->user_privileges;
	}	
	
	/////////////////////////////////////////////// MODULO LOGIN
	
	public function login($user, $pass){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT * FROM usuario WHERE usuario = '".$user."' AND senha = '".$pass."';");
			if($this->conexao->get_tot_rows() > 0){
				$this->con_result 		= $this->conexao->get_row();
				$this->id_login   		= $this->con_result->Id;
				$this->user_privileges	= $this->con_result->fgk_privilegio;
				$this->set_sessions($user, $pass);
				return true;
			}
			else return false;
		}
		else return false;
	}
	
	public function is_logado(){
		if( isset($_SESSION["user"]) && isset($_SESSION["senha"]) && $this->login($_SESSION["user"], $_SESSION["senha"]) ) return true;
		else return false;
	}
	
	public function logout(){
		$this->reset_sessions();
	}
	
	public function finalizar(){
		$this->conexao->desconecta();
	}
	
	private function set_sessions($user, $pass){
		$_SESSION["user"]   = $user;
		$_SESSION["senha"]  = $pass;
		$_SESSION["quando"] = date('d-m-Y H:i:s'); 
	}
		
	private function reset_sessions(){
		unset($_SESSION["user"]);
		unset($_SESSION["senha"]);
		unset($_SESSION["quando"]);
	}
	
	
	/////////////////////////////////////////////// MÓDULO BO
	
	public function salva_BO( $arr_dados ){
		if( $this->conexao->is_conectado() ){
						
			$tmp_edit = 0;
			
			// INSERINDO OU ALTERANDO
			if(isset($arr_dados['id_bo'])){
				$tmp_sql = "UPDATE bo";
				$tmp_sqi = "WHERE Id = ".$arr_dados['id_bo'];
				$tmp_edit = 1;
			}
			else{
				$tmp_sql = "INSERT INTO bo";
				$tmp_sqi = "";
			}
			
			$agora = date("Y-m-d H:i:s");
			
			$SQL = $tmp_sql."
			SET
				destinatario	 		= '".$arr_dados['destinatario']."',
				BOPM_No			 		= '".$arr_dados['BOPM']."',
				data_comunicacao 		= '".$arr_dados['data_comunicacao']."',
				hora_comunicacao 		= '".$arr_dados['hora_comunicacao']."',
				solicitacao_atendimento = '".$arr_dados['solicitacao_ocorrencia']."',
				provavel_descricao		= '".$arr_dados['desc_ocorrencia']."',
				classificacao			= '".$arr_dados['classificacao']."',
				local					= '".$arr_dados['endereco']."',
				local_no				= '".$arr_dados['no']."',
				bairro					= '".$arr_dados['bairro']."',
				referencia				= '".$arr_dados['referencia']."',
				data_fato				= '".$arr_dados['data']."',
				hora_fato				= '".$arr_dados['hora']."',
				hora_inicio				= '".$arr_dados['hora_inicio']."',
				hora_termino			= '".$arr_dados['hora_termino']."',
				VTR						= '".$arr_dados['vtr']."',
				historico				= '".$arr_dados['historico']."',
				guarda_integrante_1		= '".$arr_dados['nome_guarda_1']."',
				guarda_integrante_2		= '".$arr_dados['nome_guarda_2']."',
				guarda_responsavel		= '".$arr_dados['nome_guarda_responsavel']."',
				recibo_data				= '".$arr_dados['recibo_data']."',
				recibo_cargo			= '".$arr_dados['recibo_cargo']."',
				recibo_nome				= '".$arr_dados['nome_guarda_autoridade']."',
				BIG						= b'".$arr_dados['big']."',
				BOS						= b'".$arr_dados['bos']."',
				bool_analise			= 1,
				fgk_user_registro		= ".$this->id_login.",
				data_registro			= '".$agora."' $tmp_sqi;
			";
						
			$tmp_id = $this->conexao->simple_query($SQL);
			
			if(!$tmp_edit) $this->conexao->simple_query("UPDATE bo SET BO_no = '".$tmp_id.'/'.substr($agora,0,4)."' WHERE Id = ".$tmp_id.";");
			return $tmp_id;
		}
		else return false;
	}


	public function inclui_envolvido_BO($arr_dados, $id_bo)
	{
		if( $this->conexao->is_conectado() && count($arr_dados) > 0 ){	
		
			foreach($arr_dados as $att => $valor)
			{
				if( is_numeric($valor) )
					$this->conexao->simple_query("INSERT INTO envolvido_bo SET
													fgk_bo 			= ".$id_bo.",
													fgk_envolvido 	= ".$valor.";
					");
			}
		}
	}
	
	public function atualiza_envolvido_BO($arr_dados, $id_bo)
	{
		if( $this->conexao->is_conectado() ){	
			$this->conexao->simple_query("DELETE FROM envolvido_bo WHERE fgk_bo = ".$id_bo.";");
			$this->inclui_envolvido_BO($arr_dados, $id_bo);
		}
	}

	public function salva_envolvido( $arr_dados ){

		if( $this->conexao->is_conectado() ){			
								
		  $this->conexao->query("SELECT Id, cpf_cnpj FROM pessoa_envolvida WHERE cpf_cnpj = '".$arr_dados['envolvido_cpf_cnpj']."'");
		  
		  if($this->conexao->get_tot_rows() > 0){
			  $tmpRow = $this->conexao->get_row();
			  return  $tmpRow->Id;
		  }
		  else
		  {
		  					
			  $tmp_id = $this->conexao->simple_query("INSERT INTO pessoa_envolvida 
				  SET 
					  nome				= '".$arr_dados['envolvido_nome']."',
					  situacao			= '".$arr_dados['envolvido_situacao']."',
					  sexo				= '".$arr_dados['envolvido_sexo']."',
					  naturalidade		= '".$arr_dados['envolvido_naturalidade']."',
					  apelido			= '".$arr_dados['envolvido_apelido']."',
					  nascimento		= '".$arr_dados['envolvido_nascimento']."',
					  mae				= '".$arr_dados['envolvido_mae']."',
					  pai				= '".$arr_dados['envolvido_pai']."',	
					  ocupacao			= '".$arr_dados['envolvido_ocupacao']."',
					  rg				= '".$arr_dados['envolvido_rg']."',
					  orgao_expedidor	= '".$arr_dados['envolvido_orgao_expedidor']."',
					  UF				= '".$arr_dados['envolvido_uf']."',
					  escolaridade		= '".$arr_dados['envolvido_escolaridade']."',
					  cpf_cnpj			= '".$arr_dados['envolvido_cpf_cnpj']."',
					  endereco			= '".$arr_dados['envolvido_endereco']."',
					  numero			= '".$arr_dados['envolvido_numero']."',
					  complemento		= '".$arr_dados['envolvido_complemento']."',
					  bairro			= '".$arr_dados['envolvido_bairro']."',
					  municipio			= '".$arr_dados['envolvido_municipio']."',
					  UF_endereco		= '".$arr_dados['envolvido_uf_endereco']."',
					  tel_residencial	= '".$arr_dados['envolvido_tel_residencial']."',
					  celular			= '".$arr_dados['envolvido_celular']."',
					  fgk_user_registro	= ".$this->id_login.",
					  data_registro		= '".date("Y-m-d H:i:s")."';"
					);			
					return $tmp_id;
		  }
		}
		else return false;
		
	}



	private function novo_envolvido( $arr_dados, $id_bo, $no_env ){

		if( $this->conexao->is_conectado() ){
			
			$tmp_edit = 0;
			
			if(isset($arr_dados['id_bo'])){
				$tmp_sql = "UPDATE pessoa_envolvida";
				$tmp_edit = 1;
			}
			else{
				$tmp_sql = "INSERT INTO pessoa_envolvida";
				$tmp_sqi = "";
			}
			
			for($i=1; $i<=$no_env; $i++){
				
				
				// INSERINDO OU ALTERANDO
				if($tmp_edit)
					$tmp_sqi = "WHERE Id = ".$arr_dados['envolvido_id_'.$i];
				
				
				$tmp_id = $this->conexao->simple_query($tmp_sql." 
					SET 
						nome			= '".$arr_dados['envolvido_nome_'.$i]."',
						situacao		= '".$arr_dados['envolvido_situacao_'.$i]."',
						sexo			= '".$arr_dados['envolvido_sexo_'.$i]."',
						naturalidade	= '".$arr_dados['envolvido_naturalidade_'.$i]."',
						apelido			= '".$arr_dados['envolvido_apelido_'.$i]."',
						nascimento		= '".$arr_dados['envolvido_nascimento_'.$i]."',
						mae				= '".$arr_dados['envolvido_mae_'.$i]."',
						pai				= '".$arr_dados['envolvido_pai_'.$i]."',	
						ocupacao		= '".$arr_dados['envolvido_ocupacao_'.$i]."',
						rg				= '".$arr_dados['envolvido_rg_'.$i]."',
						orgao_expedidor	= '".$arr_dados['envolvido_orgao_expedidor_'.$i]."',
						UF				= '".$arr_dados['envolvido_uf_'.$i]."',
						escolaridade	= '".$arr_dados['envolvido_escolaridade_'.$i]."',
						cpf_cnpj		= '".$arr_dados['envolvido_cpf_cnpj_'.$i]."',
						endereco		= '".$arr_dados['envolvido_endereco_'.$i]."',
						numero			= '".$arr_dados['envolvido_numero_'.$i]."',
						complemento		= '".$arr_dados['envolvido_complemento_'.$i]."',
						bairro			= '".$arr_dados['envolvido_bairro_'.$i]."',
						municipio		= '".$arr_dados['envolvido_municipio_'.$i]."',
						UF_endereco		= '".$arr_dados['envolvido_uf_endereco_'.$i]."',
						tel_residencial	= '".$arr_dados['envolvido_tel_residencial_'.$i]."',
						celular			= '".$arr_dados['envolvido_celular_'.$i]."',
						fgk_bo			= ".$id_bo.",
						data_registro	= '".date("Y-m-d H:i:s")."' $tmp_sqi;"
				);			
			}
		}
		else return false;
		
	}
	
	
	public function altera_BO( $arr_dados ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("");
		}
		else return false;
	}
	
	public function delete_bo( $id ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->simple_query("DELETE FROM envolvido_bo WHERE fgk_bo = ".$id.";");
			$this->conexao->simple_query("DELETE FROM bo WHERE Id = ".$id.";");
			return true;
		}
		else return false;
	}
	
	public function finaliza_bo( $id ){
		if( $this->conexao->is_conectado() && $this->is_adm() ){
			$this->conexao->simple_query("UPDATE bo SET bool_analise = 0, bool_finalizado = 1, bool_analise = 0 WHERE Id = ".$id.";");
			return true;
		}
		else return false;
	}
	
	public function desfinaliza_bo( $id ){
		if( $this->conexao->is_conectado() && $this->is_adm() ){
			$this->conexao->simple_query("UPDATE bo SET bool_finalizado = 0, bool_analise = 1 WHERE Id = ".$id.";");
			return true;
		}
		else return false;
	}
	
	public function libera_alterar_bo( $id ){
		if( $this->conexao->is_conectado() && $this->is_adm() ){
			$this->conexao->simple_query("UPDATE bo SET bool_finalizado = 0, bool_analise = 0 WHERE Id = ".$id.";");
			return true;
		}
		else return false;
	}	

	public function busca_rapida( $dado, $ini, $limit ){
		if( $this->conexao->is_conectado() ){			
			
			$this->conexao->query("SELECT DISTINCT BO.BO_no as title, provavel_descricao as description,BO.BO_no,BO.data_fato,BO.hora_fato, BO.bool_finalizado, BO.bool_analise, BO.Id, BO.data_fato,BO.hora_fato, BO.provavel_descricao FROM BO, pessoa_envolvida AS PE, envolvido_bo as EBO WHERE 
				(BO.Id = EBO.fgk_bo AND EBO.fgk_envolvido = PE.Id) AND
				(
				BO.Id	 					LIKE '%".$dado."%' OR 
				BO.BO_no 					LIKE '%".$dado."%' OR 
				BO.destinatario	 			LIKE '%".$dado."%' OR 
				BO.BOPM_No			 		LIKE '%".$dado."%' OR 
				BO.data_comunicacao 		LIKE '%".$dado."%' OR
				BO.hora_comunicacao 		LIKE '%".$dado."%' OR 
				BO.solicitacao_atendimento  LIKE '%".$dado."%' OR 
				BO.provavel_descricao		LIKE '%".$dado."%' OR 
				BO.classificacao			LIKE '%".$dado."%' OR 
				BO.local					LIKE '%".$dado."%' OR 
				BO.local_no					LIKE '%".$dado."%' OR 
				BO.bairro					LIKE '%".$dado."%' OR 
				BO.referencia				LIKE '%".$dado."%' OR 
				BO.data_fato				LIKE '%".$dado."%' OR 
				BO.hora_fato				LIKE '%".$dado."%' OR 
				BO.hora_inicio				LIKE '%".$dado."%' OR 
				BO.hora_termino				LIKE '%".$dado."%' OR 
				BO.VTR						LIKE '%".$dado."%' OR 
				BO.historico				LIKE '%".$dado."%' OR 
				BO.guarda_integrante_1		LIKE '%".$dado."%' OR 
				BO.guarda_integrante_2		LIKE '%".$dado."%' OR 
				BO.guarda_responsavel		LIKE '%".$dado."%' OR 
				BO.recibo_data				LIKE '%".$dado."%' OR 
				BO.recibo_cargo				LIKE '%".$dado."%' OR 
				BO.recibo_nome				LIKE '%".$dado."%'
				OR
				PE.nome				LIKE '%".$dado."%' OR 
				PE.situacao			LIKE '%".$dado."%' OR 
				PE.sexo				LIKE '%".$dado."%' OR 
				PE.naturalidade		LIKE '%".$dado."%' OR 
				PE.apelido			LIKE '%".$dado."%' OR 
				PE.nascimento		LIKE '%".$dado."%' OR 
				PE.mae				LIKE '%".$dado."%' OR 
				PE.pai				LIKE '%".$dado."%' OR 	
				PE.ocupacao			LIKE '%".$dado."%' OR 
				PE.rg				LIKE '%".$dado."%' OR 
				PE.orgao_expedidor	LIKE '%".$dado."%' OR 
				PE.UF				LIKE '%".$dado."%' OR 
				PE.escolaridade		LIKE '%".$dado."%' OR 
				PE.cpf_cnpj			LIKE '%".$dado."%' OR 
				PE.endereco			LIKE '%".$dado."%' OR 
				PE.numero			LIKE '%".$dado."%' OR 
				PE.complemento		LIKE '%".$dado."%' OR 
				PE.bairro			LIKE '%".$dado."%' OR 
				PE.municipio		LIKE '%".$dado."%' OR 
				PE.UF_endereco		LIKE '%".$dado."%' OR 
				PE.tel_residencial	LIKE '%".$dado."%' OR 
				PE.celular			LIKE '%".$dado."%' OR 
				PE.data_registro	LIKE '%".$dado."%'
				);");
				
			$this->tot_results = $this->conexao->get_tot_rows();
			$this->tot_pages = ceil($this->tot_results/$limit);
			
			$this->conexao->query("SELECT DISTINCT BO.BO_no as title, provavel_descricao as description,BO.BO_no, BO.data_fato, BO.hora_fato, BO.bool_finalizado, BO.bool_analise, BO.Id, BO.provavel_descricao FROM BO, pessoa_envolvida AS PE, envolvido_bo as EBO WHERE 
				(BO.Id = EBO.fgk_bo AND EBO.fgk_envolvido = PE.Id) AND
				(
				BO.Id	 					LIKE '%".$dado."%' OR 
				BO.BO_no 					LIKE '%".$dado."%' OR 
				BO.destinatario	 			LIKE '%".$dado."%' OR 
				BO.BOPM_No			 		LIKE '%".$dado."%' OR 
				BO.data_comunicacao 		LIKE '%".$dado."%' OR
				BO.hora_comunicacao 		LIKE '%".$dado."%' OR 
				BO.solicitacao_atendimento  LIKE '%".$dado."%' OR 
				BO.provavel_descricao		LIKE '%".$dado."%' OR 
				BO.classificacao			LIKE '%".$dado."%' OR 
				BO.local					LIKE '%".$dado."%' OR 
				BO.local_no					LIKE '%".$dado."%' OR 
				BO.bairro					LIKE '%".$dado."%' OR 
				BO.referencia				LIKE '%".$dado."%' OR 
				BO.data_fato				LIKE '%".$dado."%' OR 
				BO.hora_fato				LIKE '%".$dado."%' OR 
				BO.hora_inicio				LIKE '%".$dado."%' OR 
				BO.hora_termino				LIKE '%".$dado."%' OR 
				BO.VTR						LIKE '%".$dado."%' OR 
				BO.historico				LIKE '%".$dado."%' OR 
				BO.guarda_integrante_1		LIKE '%".$dado."%' OR 
				BO.guarda_integrante_2		LIKE '%".$dado."%' OR 
				BO.guarda_responsavel		LIKE '%".$dado."%' OR 
				BO.recibo_data				LIKE '%".$dado."%' OR 
				BO.recibo_cargo				LIKE '%".$dado."%' OR 
				BO.recibo_nome				LIKE '%".$dado."%'
				OR
				PE.nome				LIKE '%".$dado."%' OR 
				PE.situacao			LIKE '%".$dado."%' OR 
				PE.sexo				LIKE '%".$dado."%' OR 
				PE.naturalidade		LIKE '%".$dado."%' OR 
				PE.apelido			LIKE '%".$dado."%' OR 
				PE.nascimento		LIKE '%".$dado."%' OR 
				PE.mae				LIKE '%".$dado."%' OR 
				PE.pai				LIKE '%".$dado."%' OR 	
				PE.ocupacao			LIKE '%".$dado."%' OR 
				PE.rg				LIKE '%".$dado."%' OR 
				PE.orgao_expedidor	LIKE '%".$dado."%' OR 
				PE.UF				LIKE '%".$dado."%' OR 
				PE.escolaridade		LIKE '%".$dado."%' OR 
				PE.cpf_cnpj			LIKE '%".$dado."%' OR 
				PE.endereco			LIKE '%".$dado."%' OR 
				PE.numero			LIKE '%".$dado."%' OR 
				PE.complemento		LIKE '%".$dado."%' OR 
				PE.bairro			LIKE '%".$dado."%' OR 
				PE.municipio		LIKE '%".$dado."%' OR 
				PE.UF_endereco		LIKE '%".$dado."%' OR 
				PE.tel_residencial	LIKE '%".$dado."%' OR 
				PE.celular			LIKE '%".$dado."%' OR 
				PE.data_registro	LIKE '%".$dado."%'
				) LIMIT ".$ini.",".$limit.";"
			);

			$this->tot_page = $this->conexao->get_tot_rows();
		}
		else return false;
	}
	
	public function get_bo( $id ){
		if( $this->conexao->is_conectado() )
		{
			$this->conexao->query("SELECT * FROM bo WHERE Id = ".$id.";");
			if( $this->conexao->get_tot_rows() <= 0 ) return false;
			else
			{
				return $this->conexao->get_row();
			}
		}
		else return false;
	}
	
	public function get_envolvidos( $id_bo ){
		if( $this->conexao->is_conectado() )
		{
			if( $this->get_bo( $id_bo ) != false)
			{
				$this->conexao->query("SELECT * FROM envolvido_bo as EBO, pessoa_envolvida as PE
									   WHERE EBO.fgk_bo = ".$id_bo." AND EBO.fgk_envolvido = PE.Id;");
				$this->tot_results = $this->conexao->get_tot_rows();
				if( $this->conexao->get_tot_rows() <= 0 ) return false;
				else return true;
			}
		}
		else return false;
	}
	
	public function listaBos($ini, $limit)
	{
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT * FROM bo;");
			$this->tot_results = $this->conexao->get_tot_rows();
			$this->tot_pages = ceil($this->tot_results/$limit);
			
			$this->conexao->query("SELECT * FROM bo LIMIT ".$ini.",".$limit.";");
			$this->tot_page = $this->conexao->get_tot_rows();
			return true;
		}
		else return false;
	}
	

	//// PARA AUTOCOMPLETAR AJAX
	// campo title
	
	public function busca_endereco( $dado, $ini, $limit ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT local as title, local, local_no, bairro, referencia FROM bo WHERE 
								   local 		LIKE '%".$dado."%'
								  LIMIT ".$ini.",".$limit.";");
		}
	}

	public function busca_endereco2( $dado, $ini, $limit ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT endereco as title, endereco, numero, bairro, complemento, municipio, UF_endereco FROM pessoa_envolvida WHERE 
								   endereco		LIKE '%".$dado."%' 
								  LIMIT ".$ini.",".$limit.";");
		}
	}
	
	public function busca_descricao( $dado, $ini, $limit ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT CONCAT(codigo,' - ',descricao) as title, descricao as provavel_descricao FROM provavel_descricao WHERE 
								   descricao LIKE '%".$dado."%' OR
								   codigo LIKE '%".$dado."%'
								  LIMIT ".$ini.",".$limit.";");
		}
	}
	
	public function busca_cpf( $dado, $ini, $limit ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT *, DATE_FORMAT(nascimento, '%d/%m/%Y') as nascimento, cpf_cnpj as title, nome as description FROM pessoa_envolvida WHERE 
								   cpf_cnpj LIKE '%".$dado."%' 
								  LIMIT ".$ini.",".$limit.";");
		}
	}
	
	public function busca_guardas( $dado, $ini, $limit ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT *, nome as title FROM guarda WHERE 
								   nome 		LIKE '%".$dado."%'  OR
								   matricula 	LIKE '%".$dado."%'
								  LIMIT ".$ini.",".$limit.";");
		}
	}
	/////////////////////////////////////////////// MÓDULO PAGINACAO
	
	public function getTotPages()
	{
		return $this->tot_pages;
	}


	public function getTotResults()
	{
		return $this->tot_results;
	}
	
	public function getTotPage()
	{
		return $this->tot_page;
	}

	/////////////////////////////////////////////// MÓDULO USUARIOS

	public function novo_user( $arr_dados ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->simple_query("
				INSERT INTO usuario SET
					usuario		  		= '".$arr_dados['usuario']."',
					senha		  		= '".$arr_dados['senha']."',
					nome		  		= '".$arr_dados['nome']."',
					fgk_privilegio 		= ".$arr_dados['tipo'].",
					fgk_user_registro	= ".$this->id_login.",
					data_registro 		= '".date("Y-m-d H:i:s")."';"
			);
		}
		else return false;
	}


	public function novo_guarda( $arr_dados ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->simple_query("
				INSERT INTO guarda SET
					nome		  		= '".$arr_dados['nome']."',
					matricula	  		= '".$arr_dados['matricula']."',
					fgk_user_registro	= ".$this->id_login.",
					data_registro 		= '".date("Y-m-d H:i:s")."';"
			);
		}
		else return false;
	}


	public function novo_provavel_desc( $arr_dados ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->simple_query("
				INSERT INTO provavel_descricao SET
					descricao	  		= '".$arr_dados['descricao']."',
					codigo		  		= '".$arr_dados['cod']."',
					fgk_user_registro	= ".$this->id_login.",
					data_registro 		= '".date("Y-m-d H:i:s")."';"
			);
		}
		else return false;
	}

	public function altera_user( $arr_dados ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("");
		}
		else return false;
	}
	
	public function delete_user( $id ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->simple_query("DELETE FROM usuario WHERE Id = ".$id.";");
			return true;
		}
		else return false;
	}


	public function delete_guarda( $id ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->simple_query("DELETE FROM guarda WHERE Id = ".$id.";");
			return true;
		}
		else return false;
	}


	public function delete_provavel_desc( $id ){
		if( $this->conexao->is_conectado() ){
			$this->conexao->simple_query("DELETE FROM provavel_descricao WHERE Id = ".$id.";");
			return true;
		}
		else return false;
	}

	public function lista_privilegios(){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT * FROM privilegio;");
		}
		else return false;
	}

	public function lista_users(){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT usuario.Id as Id, nome, descricao, usuario FROM usuario 
								   INNER JOIN privilegio
								   ON usuario.fgk_privilegio = privilegio.Id;");
		}
		else return false;
	}

	public function lista_guardas(){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT * FROM guarda;");
		}
		else return false;
	}

	public function lista_provavel_desc(){
		if( $this->conexao->is_conectado() ){
			$this->conexao->query("SELECT * FROM provavel_descricao;");
		}
		else return false;
	}	
	
	public function getConResult()
	{
		return $this->con_result;
	}
	
	public function eachResult()
	{
		return $this->conexao->get_row();
	}
	
	
}