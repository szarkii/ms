<?php

  function isElementInArray ($arr, $elem) {
    $n = count($arr);
    for ($i=0; $i<$n; $i++) {
      if ($arr[$i] == $elem)
        return true;
    }
    return false;
  }

  $items = simplexml_load_file("server/xml/oferta.xml");

  if ( isset($_GET["id"]) )
    $itemID = $_GET["id"];
  else
    $itemID = $items -> produkt[0] -> id;

  if ( isset($_GET["sort"]) )
    $sortBy = $_GET["sort"];
  else
    $sortBy = "zastosowanie";



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

  <title>Oferta - Multi Service</title>
</head>
<body>

  <?php include ("server/php/top.php"); ?>

<div class="container">

  <div class="col-sm-3" style="padding-left: 0">

    <div class="panel panel-primary">
      <div class="panel-heading">Menu</div>
      <div class="panel-body">

        <?php
          $categories[0] = (string)$items -> produkt[0] -> $sortBy;

          foreach ($items as $item) {

            if ( count($item -> $sortBy -> children()) > 0 ) {
              $subValueName = $item -> $sortBy -> children() -> getName();
              foreach ($item -> $sortBy -> children() as $child) {
                if ( isElementInArray($categories, $child) )
                  continue;
                else
                  $categories[] = (string)$child;
              }
            }

            else {
              if ( isElementInArray($categories, $item -> $sortBy) )
                  continue;
                else
                  $categories[] = (string)$item -> $sortBy;
            }



            /*
            $add = true;
            $n = count($categories);
            for ($i=0; $i<$n; $i++) {
              if ($categories[$i] == $item -> $sortBy) {
                $add = false;
                break;
              }
            }

            if ($add)
              $categories[] = (string)$item -> $sortBy;

            */
          }


          $n = count($categories);
          for ($i=0; $i < $n; $i++) { 
            echo "<div class='well'>" . $categories[$i] . "</div>";
            if ( isset($subValueName) )
              $categoryItems = $items -> xpath("produkt/" . $sortBy . "[" . $subValueName . "='" . $categories[$i] . "']/parent::*");
            else
              $categoryItems = $items -> xpath("produkt[$sortBy='" . $categories[$i] . "']");
            foreach ($categoryItems as $item) {
              echo $item -> nazwa;
              echo "<br>";
            }
          }

        ?>


      </div>
    </div>
    
  </div>

  <div class="col-sm-9" style="padding: 0">

    <div class="panel panel-primary">
      <div class="panel-heading">Oferta</div>
      <div class="panel-body">  
        <?php

          //print_r($items);

          $currentItem = $items -> xpath("produkt[id=" . $itemID . "]");

          foreach ($currentItem[0] as $key => $value) {
            echo $key . ": " . $value . "<br>";
          }


          $test = "splot";

          if ( count($items -> produkt[1] -> $test -> children()) > 0 )
            echo "ma dzieci";
          else
            echo "nie ma dzieci";


          // foreach ($items -> produkt[1] -> zastosowanie -> children() as $item) {
          //   echo $item;
          //   echo "<br>";
          // }


        ?>
      </div>
    </div>
  
  </div>

</div>

  <?php include ("server/php/bottom.php"); ?>

</body>
</html>

