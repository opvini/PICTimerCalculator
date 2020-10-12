
$(document).ready(function(e) {
    
	hljs.initHighlightingOnLoad();
	
	var $pre = new Array(2,4,8,16,32,64,128,256);
	
	$('#btn_calc').click(function(){
		
		var $xt = $('#xt').val();
		var $f  = $('#f').val();
		var $r  = '';
		var $c  = 0;		
		
		if( $xt != '' && $f != '' )
		{
		
		  for(var $i=0; $i<$pre.length;$i++){
			  for(var $j=1; $j<65536; $j++){
				  if( ($xt/4)/$pre[$i]/$j == $f ){
					  if( $c < 1){
						  $('#prescaler').html(''+$pre[$i]);
		  				  $('.preload').html(''+65536-$j);
						  $c++;
					  }
					  $r +=  "<tr><td>DIV_"+$pre[$i]+"</td><td>"+(65536-$j)+"</td><td>65536-"+$j+"</td></tr>";
				  }
			  }
		  }
		  
		  if($c > 0)
		  {
			  $results = $('#table_results').find('tbody');
			  $results.empty();
			  $results.append($r);
			  
			  $('#frequency').html(''+$f);
			  $('#clock').html(''+$xt);
			  if($xt>4000000) $('#xtal').html('HS'); else $('#xtal').html('XT');
			  
			  $('pre code').each(function(i, block) {
				hljs.highlightBlock(block);
			  });
			  
			  $('#modal_ok').modal('show');
		  }
		  else $('#modal_fail').modal('show');
		  
		}
		
	});
	  
});