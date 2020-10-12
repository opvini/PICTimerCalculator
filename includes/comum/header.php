<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">


<head>

  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  
  <title><?php print $_titulo ?></title>

  <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700|Open+Sans:300italic,400,300,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="includes/semantic-ui/packaged/css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="includes/plugins/search.css">
  
  <link rel="stylesheet" type="text/css" href="includes/css/estilos.css">
  <?php if(isset($_includes_css)) for($i=0; $i<count($_includes_css); $i++) print '<link rel="stylesheet" type="text/css" href="'.$_includes_css[$i].'">'; ?>

  <script src="includes/js/jquery-2.1.1.min.js"></script>

  <script src="includes/semantic-ui/packaged/javascript/semantic.min.js"></script>
  <script src="includes/js/comum.js"></script>
  <script src="includes/plugins/search.js"></script>
  <script src="includes/plugins/api.min.js"></script>
  
  <?php if(isset($_includes_js)) for($i=0; $i<count($_includes_js); $i++) print '<script src="'.$_includes_js[$i].'"></script>'; ?>
  
  
</head>



<body>
