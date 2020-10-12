<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta name="description" content="PIC Timer Calculator (PTC), by Vinícius Lage, calculates the prescaler and preload to your microcontroler firmware and gerates a sample code based on CCS compiler." />
  <meta name="keywords" content="PIC, timer calculator, microcontroler, prescaler calculator, preload calculator, CCS" />
  
  <title>PIC Timer Calculator v1.0 - by Vinicius Lage</title>

  <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700|Open+Sans:300italic,400,300,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="includes/Semantic-UI-1.0.0/dist/semantic.min.css">
  
  <link rel="stylesheet" type="text/css" href="includes/css/estilos.css">
  <link rel="stylesheet" type="text/css" href="includes/plugins/highlight.css">

  <script src="includes/js/jquery-2.1.1.min.js"></script>
  <script src="includes/Semantic-UI-1.0.0/dist/semantic.min.js"></script>
  <script src="includes/plugins/highlight.js"></script>

  <script src="includes/js/calc.js"></script>

</head>

<body>


<div id="espaco_topo">
	<h2 class="ui dividing header"><i class="bug icon"></i>PIC Timer Calculator v1.0</h2>
    <div class="ui basic segment">
      <h4 class="ui header">Basic Info</h4>
      <p>PTC, by Vinícius Lage, calculates the prescaler and preload to your microcontroler firmware and gerates a sample code based on CCS compiler.</p>
    </div>
</div>


<form class="ui form segment" id="form_calc" method="post" action="entrar.php" >

  <div class="ui error message"></div>

  <div class="field">
    <label>Your Crystal Frequency:</label>
    <div class="ui right labeled left icon input">
      <i class="setting icon"></i>
      <input type="text" placeholder="4000000" id="xt">
      <div class="ui tag label">Hz</div>
    </div>
  </div>
  
  <div class="field">
    <label>What Frequency Do You Need:</label>
    <div class="ui right labeled left icon input">
      <i class="wizard icon"></i>
      <input type="text" placeholder="50" id="f">
      <div class="ui tag label">Hz</div>
    </div>
  </div>

  <div class="ui blue button" id="btn_calc">Calculate and show me the code</div>
</form>


<div class="ui modal" id="modal_ok">
  <i class="close icon"></i>
  <div class="header">PIC Timer Calculator v1.0 - by Vinícius Lage</div>
  
  <div class="content">
    <div class="description">
      
      <div class="ui header">Here are your results. Choose one and enjoy!</div>
      
      <p>
      These results are based on Timer0 and PIC 18F4550.<br />
      But probably will work on yout microcontroller.<br />
	  Use prescaler and preload.
      </p>
      
      <table class="ui table" id="table_results">
        <thead>
          <tr>
            <th>Prescaler</th>
            <th>Preload (16bit)</th>
            <th>Why?</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
      
      <div class="ui header">Here is an example of code for your CCS firmware:</div>
      
    <pre><code class="cpp">
    //////////////////////////////////////////////////////
    //
    // Code by PIC Timer Calculator 
    // Developed by Vinícius Lage from Brazil.
    // Email: op.vini@gmail.com
    //
    // Generate a <span id="frequency"></span>Hz Timer0 interrupt
    //
    //////////////////////////////////////////////////////
    
    #include <18F4550.h>
    #fuses <span id="xtal"></span>,NOWDT,PUT,NOBROWNOUT,NOLVP 
    #use delay(clock=<span id="clock"></span>)
    
    void main(){
      setup_timer_0( RTCC_INTERNAL | RTCC_DIV_<span id="prescaler"></span> );
      set_timer0( <span class="preload"></span> ); 
      
      enable_interrupts( INT_TIMER0 );
      enable_interrupts( GLOBAL );
    }
    
    #int_timer0
    void intTimer0(){
      set_timer0( <span class="preload"></span> ); 
      // put here your code
      // this code will run on the frequecy that you desire 
    }
    </code></pre>
      
    </div>
  </div>
  
  <div class="actions">
    <div class="ui positive right labeled icon button">
      Ok, thanks a lot Vinícius
      <i class="checkmark icon"></i>
    </div>
  </div>
  
</div>


<div class="ui modal" id="modal_fail">
  <i class="close icon"></i>
  <div class="header">PIC Timer Calculator v1.0 - by Vinícius Lage</div>
  
  <div class="content">
    <div class="description">
      
    <div class="ui header">No results</div>
    <p>Try other values for frequecy.<br /></p>

  <div class="actions">
    <div class="ui positive right labeled icon button">
      Ok Vinicius, I will try another frequency
      <i class="checkmark icon"></i>
    </div>
  </div>
  
</div>

</body>
</html>
