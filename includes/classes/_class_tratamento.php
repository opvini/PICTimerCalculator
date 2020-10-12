<?php

//**************************************
//
// Criado por: Vinícius Nunes Lage
// Criada em: 08/05/2014
//
//**************************************


class Tratamento{
	
	public function data_brasil($str){
		if(trim($str)!="")
			return substr($str, 8, 2)."/".substr($str, 5, 2)."/".substr($str, 0, 4);
	}
	
	public function time_brasil($str){
		return substr($str, 0, 2).":".substr($str, 3, 2);
	}
	
}
