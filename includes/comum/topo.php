
<div id="principal_topo">

<div id="logo_guarda"></div>

<div id="principal_menu">

<div class="ui menu">

  <a class="<?php if($_current_page=="principal") print "active ";?>item" href="principal.php">
    <i class="home icon"></i> Início
  </a>
  
  <div class="ui pointing dropdown link <?php if($_current_page=="forms")print "active ";?>item">
    <i class="icon file"></i>Formulários <i class="dropdown icon"></i>
    <div class="menu">
      <a class="item" href="form_big.php">BIG - Boletim de Intervenção</a>
      <a class="item" href="form_big.php?bos=1">BOS - Boletim Simplifcado</a>
      
      <?php if( $GMOP->get_privileges() == 1 ){ ?>
      <a class="item" href="lista_guardas.php">Cadastro de Guardas</a>      
      <a class="item" href="lista_provavel_desc.php">Cadastro de Prováveis Descrições</a>
      <?php } ?>

      <a class="item" href="lista_bos.php">Ver Todos os BO's Registrados</a>
    </div>
  </div>
  
   <?php if( $GMOP->get_privileges() == 1 ){ ?>
  <a class="<?php if($_current_page=="users")print "active ";?>item" href="lista_usuarios.php">
    <i class="icon user"></i> Usuários
  </a>
  <?php } ?>
   
  <div class="right menu">            
      
  <div class="ui autocomplete search item">
    <div class="ui icon input">
  	<form id="form_busca" method="get" action="lista_bos.php">
      <input class="prompt" type="text" placeholder="Localizar..." data-autocomplete-fields="opt=search" data-autocomplete-url="ajax.php" name="q" id="field_busca">
    </form>
      <i class="search icon" id="btn_search"></i>
    </div>
    <div class="results"></div>
  </div>
      
      <div class="ui dropdown item">
        <i class="icon setting"></i>
        <div class="menu">
          <a class="item" href="lista_usuarios.php"><i class="icon edit"></i>Meus Dados</a>
          <a class="item" href="principal.php"><i class="icon help"></i>Ajuda</a>
          <a class="item" href="logout.php"><i class="icon off"></i>Sair</a>
        </div>
   </div>
      
  </div>
</div>

<div id="brasao_guarda_topo"></div>

</div>
</div>