<?php

//**************************************
//
// Adaptado por: Vinícius Nunes Lage
// Criada em: 09/06/2014
//
//**************************************


//**************************************
//
// getAllValues():
//  retorna um array do tipo arr[NOME_CAMPO] = VALOR  
//
// getValue($campo):
//  retorna o valor do campo especificado
//
// add( $arr1, $arr2, ... ):
//  adiciona campos ao formulário, com filtros e mensagens de erro cada
//
// isValid():
//  testa se o formulário é válido
//
// getTotErrors($field):
//  retorna um array com os erros do campo especificado
//
// getTotFieldsWithErros():
//  retorna o total de campos com erros
//
// getError($field,$index):
//  retorna o erro do indice index
//
// getErrorById($index):
//  retorna o erro de índice index
//
// countError():
//  retorna o número de erros gerados
//
// getFieldWithError($index):
//  retorna o campo que contem erro de indice index
//
// getTotErrorsByField($field):
//  retorna o total de erros do campo field
//
//
/////////////// PROPERTIES
// name
// method (post, get)
//
//
////////////////////////////////// FILTERS AND CONDITIONS
//
/////////////// CONDITIONS
// required
// maxlength
// minlength
// eqlength
// equal
// numeric
// int
// float
// min
// max
// date
// url
// ip
//	
/////////////// FILTERS	
// striptags
// addslashes
// trim
// tolower
// toupper
// UTF-8
// md5
// dateBr
//
//**************************************






class Form{
		
	private $_errors;
    private $_messages;
	private $_defaultDateFormat;
	private $_fields;
	private $_arrAllFields;
	private $_arrFieldsErrors;
	private $_arrFieldsWithErrors;
	private $_totErrors;
	
	private $_data;
    private $_curElement;
	
	
	public function __construct()
	{
		$this->Reset();
	    $this->setDefaultDateFormat("ddmmyyyy");
	}
	
	public function Reset()
	{
		$this->_errormessages		= array();
		$this->_fields				= array();
		$this->_messages			= array();
		$this->_arrAllFields		= array();
		$this->_arrFieldsErrors		= array();
		$this->_arrFieldsWithErrors = array();
	}

    public function setDefaultDateFormat($format)
    {
        $this->_defaultDateFormat = $format;
    }
	
	public function getValue($element)
	{
		if(isset($this->_fields[$element]["value"])) return $this->_fields[$element]["value"];
	}
	
	
	public function getAllValues()
	{
		$this->_arrAllFields = array();
		
		foreach( $this->_fields as $type => $value )
		{
			$this->_arrAllFields[$this->_fields[$type]["name"]] = $this->_fields[$type]["value"];
		}
		
		return $this->_arrAllFields;
	}
	
	
	public function getTotErrors()
    {
		return $this->_totErrors;
    }
	
	public function getTotFieldsWithErros()
    {
        return count($this->_messages);
    }
	
	
	public function ErrorFields()
    {
        return array_keys($this->_messages);
    }
		
	public function getFieldWithError($index)
    {
		if(isset($this->_arrFieldsWithErrors[$index])) return $this->_arrFieldsWithErrors[$index];
    }
	
	public function getTotErrorsByField($field){
		if( isset($this->_arrFieldsErrors[$field]) ) return $this->_arrFieldsErrors[$field];
		else return 0;
	}
	
	public function getErrorById($index){
		if( isset($this->_errors[$index]) ) return $this->_errors[$index];
	}
	
	public function getError($field, $index){
		if( isset($this->_messages[$field][$index]) ) return $this->_messages[$field][$index];
	}
	
	////////////////////////////////////////////////////////////////// cria o array com campos e o total de erros de cada
	private function saveArrFieldsErrors()
	{
		$this->_arrFieldsErrors		= array();
		$this->_arrFieldsWithErrors	= array();
		
		foreach( $this->_messages as $type => $value){
			$this->_arrFieldsErrors[$type] = count( $this->_messages[$type] );
			$this->_arrFieldsWithErrors[]  = $type;
		}
		
		return $this->_arrFieldsErrors;
	}
	
	
	////////////////////////////////////////////////////////////////// conta e salva o total de erros
	private function saveTotErrors(){
		$this->_totErrors = 0;
		foreach( $this->_arrFieldsErrors as $type => $value )
			$this->_totErrors += $value;
	}
	
	////////////////////////////////////////////////////////////////// salva todos os erros em errors[]
	private function saveAllErrors()
	{
		$this->_errors = array();
		foreach($this->_messages as $type => $val){
			foreach($this->_messages[$type] as $index => $errMsg){
				$this->_errors[] = $errMsg;
			}
		}
	}
	
	
	////////////////////////////////////////////////////////////////// recebe os campos e suas respectivas regras
	public function add( /* $arrays fields */ )
	{
		$tot_args = func_num_args();
		for($i=0;$i<$tot_args;$i++)
		{
			$arr_tmp = func_get_arg($i);	
			$this->_fields[$arr_tmp["name"]] = $arr_tmp;
		}
	}
	
	
	////////////////////////////////////////////////////////////////// verifica se o formulario eh valido
	public function isValid()
	{
		$this->receiveForm();
		return $this->verifyRules();
	}
    
	
	////////////////////////////////////////////////////////////////// verifica o metodo e e recebe os valores do formulario
	private function receiveForm()
	{
		foreach( $this->_fields as $type => $value )
		{
			if( isset($value["method"]) && strtoupper($value["method"]) == "GET" )
			{
				if( !isset($_GET[$value["name"]]) ) 
					$this->_fields[$type]["value"] = "";
				else
					$this->_fields[$type]["value"] = $_GET[$value["name"]];
			}
			else
			{
				if( !isset($_POST[$value["name"]]) ) 
					$this->_fields[$type]["value"] = "";
				else
					$this->_fields[$type]["value"] = $_POST[$value["name"]];
			}
		}
	}
	
	
	////////////////////////////////////////////////////////////////// verifica todos as regras para cada campo
	private function verifyRules()
	{
		$valid = true;
		
		foreach( $this->_fields as $type => $value )
		{
			foreach( $value["filters"] as $filter => $filter_value )
			{
				
				if( $this->checkRule( $filter, $filter_value, $value["value"], $type ) )
				{
				}
				else
				{
					if( isset( $this->_fields[$type]["errMsg"][$filter] ) )
						$this->_messages[$type][] = $this->_fields[$type]["errMsg"][$filter];
					else
						$this->_messages[$type][] = $this->DefaultErrorMsg($filter);
					
					$valid = false;
				}
			}
		}
		
		$this->saveArrFieldsErrors();
		$this->saveTotErrors();
		$this->saveAllErrors();
		return $valid;
	}
	
	
	
	////////////////////////////////////////////////////////////////// motor para verificacao das regras
	
	private function checkRule($filter, $filter_value=true, $value, $element)
	{
		switch(strtolower($filter))
		{
			case "required"		:	return !(trim($value)=="");
			case "maxlength"	:	return (strlen($value)<=$filter_value);
			case "minlength"	:	return (strlen($value)>=$filter_value);
			case "eqlength"		:	return (strlen($value)==$filter_value);	
			case "equal"		:	return ($value==$filter_value);
			case "numeric"		:	return is_numeric($value);
			case "int"			:	return $this->isInt($value);	
			case "float"		:	$v = (float) $value; return ((string)$v===(string)$value);
			case "min"			:	if($value<$filter_value) return false; 	break;
			case "max"			:	if($value>$filter_value) return false; 	break;
            case "email"        :   return preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9]+$/",$value,$m);
			
			case "date"         :   if($filter_value == true) return $this->isDate($value, $this->_defaultDateFormat); 
									else return $this->isDate($value,$filter_value); 
									
			case "url"          :   return true; 
			case "ip"           :   return true; 
			
			case "striptags"	: 	$this->_fields[$element]["value"] = strip_tags($this->_fields[$element]["value"]);  return true;
			case "addslashes"	:	$this->_fields[$element]["value"] = addslashes($this->_fields[$element]["value"]);  return true;
			case "trim"			:	$this->_fields[$element]["value"] = trim($this->_fields[$element]["value"]); 	    return true;
			case "tolower"		:	$this->_fields[$element]["value"] = strtolower($this->_fields[$element]["value"]);  return true;
			case "toupper"		:	$this->_fields[$element]["value"] = strtoupper($this->_fields[$element]["value"]);  return true;
			case "UTF-8"		:	$this->_fields[$element]["value"] = utf8_decode($this->_fields[$element]["value"]); return true;
			case "md5"			:   $this->_fields[$element]["value"] = md5($this->_fields[$element]["value"]); 		return true;
			case "datebr"		:	$this->_fields[$element]["value"] = $this->changeDateFormatBrazil($this->_fields[$element]["value"]); return true;
			
			default: return true;
		}
	}
	
	
	
	////////////////////////////////////////////////////////////////// mensagens pre-definidas de erro	
	private function DefaultErrorMsg($rule)
    {
        switch(strtolower($rule))
        {
            case "required"     :    return "Required";      
            case "maxlength"    :    return "Over Length";        
            case "minlength"    :    return "Under Length";       
            case "eqlength"     :    return "Length Mismatch";      
            case "equal"        :    return "Data Mismatch";
            case "numeric"      :    return "Numeric Value Require";
            case "int"          :    return "Integer Value require";
            case "float"        :    return "Float Value require";
            case "min"          :    return "Too small";
            case "max"          :    return "Too high";
            case "date"         :    return "Invalid Date";
            case "email"        :    return "Invalid Email address";
			case "url"	        :    return "Invalid URL";
			case "ip"	        :    return "Invalid IP";
            default             :    return "error";
        }        
        return true;
    }
	
	
	
	////////////////////////////////////////////////////////////////// verifica se eh um numero inteiro
	private function isInt($number)
    {
        $text = (string)$number;
        $textlen = strlen($text);
        if ($textlen==0) return 0;
        for ($i=0;$i < $textlen;$i++)
        { 
            $ch = ord($text{$i});
            if (($ch<48) || ($ch>57)) 
                return 0;
        }
        return 1;
    }
	
	////////////////////////////////////////////////////////////////// altera o formato da data - FILTRO
	// recebe no padrão brasileiro dd/mm/yyyy
	// retorna no padrão americano para BD yyyy/mm/dd
	private function changeDateFormatBrazil($value){
		$d = $value;

		if(preg_match("/^((\d){1,4})[\/\.-]((\d){1,2})[\/\.-](\d{2,4})$/",$d,$matches))
		{
			$T = explode("/",$value);
			return $T[2]."-".$T[1]."-".$T[0];
		}
		else return "";
	}

    ////////////////////////////////////////////////////////////////// verifica se eh uma data valida
    private function isDate($value, $format)
	{
        $d = $value;
		
		if(!preg_match("/^((\d){1,4})[\/\.-]((\d){1,2})[\/\.-](\d{2,4})$/",$d,$matches))
			return false;
			
		$T = explode("/",$d);
        
        switch($format)
        {
            case "mmddyyyy":
                $M = (int)$T[0];
                $D = (int)$T[1];
                $Y = (int)$T[2];
                if($Y<999)
                    return false;

            break;
            case "mmddyy":
                //print_r($T);
                $M = (int)$T[0];
                $D = (int)$T[1];
                $Y = (int)$T[2];
                if($Y>99)
                    return false;
				else if( $Y > date("y") ) $Y = 1000+$Y;
				else $Y = 2000+$Y;
				
            break;
            case "ddmmyyyy":
                $D = (int)$T[0];
                $M = (int)$T[1];
                $Y = (int)$T[2];
                if($Y<999)
                    return false;
            break;
            case "ddmmyy":
                $D = (int)$T[0];
                $M = (int)$T[1];
                $Y = (int)$T[2];
                if($Y>99)
                    return false;
				else if( $Y > date("y") ) $Y = 1000+$Y;
				else $Y = 2000+$Y;
            break;
            case "yyyymmdd":
                $Y = (int)$T[0];
                $M = (int)$T[1];
                $D = (int)$T[2];
                if($Y<999)
                    return false;
            break;
        }
            
		return checkdate($M, $D, $Y);
	}
	
	
	
	////////////////////////////////////////////////////////////////// regra para um campo dependente de outro
    function depend($con,$value)    
    {
        if($this->check("require",$this->_data[$con['depend_on']]))
        {
            $valid = true;
            $curErr = array();
			
            foreach($con as $rule=>$con)
            {
                if($rule!='depend_on')                
                    if(!$this->check($rule,$value,$con))
                    {
                        $valid = false;
                        if(isset($errormessage[$rule]))
                            $curErr[]=$errormessage[$rule];
                        else $curErr[]=$this->DefaultErrorMsg($rule);
                    }
            }
            if(!$valid)
                $this->_messages[$this->_curElement] = $curErr;
            return $valid;
        }
        else return true;
    }
	
	
	public function debug(){
		$i=0;
		print "Ocorreram ".$this->getTotErrors()." erros.<br>
		   O total de campos com erros são: ".$this->getTotFieldsWithErros()."<br><br>";
	
		for($i=0; $i < $this->getTotErrors(); $i++)
			print $this->getErrorById($i)."<br>";
					
		for($i=0; $i<$this->getTotFieldsWithErros(); $i++)
			print "O campo ".$this->getFieldWithError($i)." teve ".$this->getTotErrorsByField( $this->getFieldWithError($i) )." erros. <br>";
			
		print( $this->getError('nome_guarda_autoridade',0) );
	}
		
}

?>