<?php
  /*
  $struct = simplexml_load_file('set.xml');

  $struct -> from = "Janusz Cebulka aka Twarożek";

  echo $struct -> from;

  unset ($struct -> to);

  echo $struct -> asXML();

  
  $f = fopen("set.xml", "w");
  fwrite($f, $struct -> asXML());
  fclose($f);
  */

  $file = simplexml_load_file("server/xml/index.xml");

?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="style/style.css" >

  <title>Multi Service - Tkaniny i Inspiracje</title>
</head>
<body>

  <?php include ("server/php/top.php"); ?>

<div class="container">
    
  <div class="panel panel-primary">
    <div class="panel-heading">Strona Główna</div>
    <div class="panel-body">  
      <?php
        echo $file -> content;

        echo "<br>";
        echo ord('ȃ');
        echo chr(200);
        echo chr( ord('ȃ') - 3 );
      ?>
    </div>
  </div>

</div>

  <?php include ("server/php/bottom.php"); ?>

</body>
</html>

