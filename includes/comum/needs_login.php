<?php

$_current_sis = "";

include_once("includes/classes/_class_guarda.php");
include_once("controle_acesso.inc.php");

$GMOP = new Guarda;

if( !$GMOP->is_logado() )
{
	$GMOP->finalizar();
	header("Location: login.php");
	exit;
}

if( (isset($arrAcesso[ $_current_sis ]) && $GMOP->get_privileges() <= $arrAcesso[ $_current_sis ]) )
	exit("Sem premissao.");

?>