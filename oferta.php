<?php

  class Items {

    private $pathXMLfile = "server/xml/oferta.xml";
    private $items;     // obiekt XML


    public function Items() {
      $this->items = simplexml_load_file( $this->pathXMLfile );
    }

    public function getItemsObject () {
      return $this->items;
    }

    public function getItem ($id) {
      return $this->items -> produkt[$id];
    }

    // public function hasChild ($path) {
    //   $p = "produkt -> id";
    //   echo $this->items -> $p;
    // }

    public function getAllTagValues ( $tagName ) {

      $allTagValues = array();

      foreach ($this->items -> produkt as $item) {
      
        $children = $item -> $tagName -> children();

        if ( @count( $children ) > 0 ) {

          foreach ($children as $child) {
            $child = (string) $child;
            if ( !in_array($child, $allTagValues) )
              $allTagValues[] = $child;
          }

        }
        else
          if ( !in_array( ( (string) $item -> $tagName ), $allTagValues) )
            $allTagValues[] = (string) $item -> $tagName;
      }

      return $allTagValues;
    }


    // public function getByPath ( $path, $tagName, $tagValue ) {
    //   $tmp = @$this->items -> xpath( $path . "[" . $tagName . "=" . $tagValue . "]" );
    //   if ( $tmp != FALSE )
    //     return $tmp;
    //   else {
    //     $childName = $this->items -> $path
    //     //$tmp = 
    //   }
    //   //echo $path . "[" . $tagName . "=" . $tagValue . "]";
    //   //return $tmp;
    // }

    public function getByPath ( $path ) {
      return $this->items -> xpath($path);
    }

    public function getItemsNamesByPath ( $path, $tagName, $tagValue ) {
            $children = $this->items -> produkt -> $tagName -> children();
            //$children = $children -> produkt -> $sortBy -> children();

            if ( @count( $children ) > 0 ) {
              $itemsInCategory = 
                $this->items -> xpath("produkt/" . $tagName . "[" . $tagName . "='" . $tagValue . "']/parent::*");
              //$itemsInCategory = 
              //  @$items -> getByPath ( "produkt/" . $sortBy . "[" . $sortBy . "='" . $categories[$i] . "']/parent::*" );
            }
            else {
              //echo "#### 1 #####<br>";
              //$itemsInCategory = (@$items -> getByPath( "produkt[" . $sortBy . "='" . $categories[$i] . "']" );
              $itemsInCategory = $this->items -> xpath( "produkt[" . $tagName . "='" . $tagValue . "']" );
            }

            if ($itemsInCategory != NULL) {
              $itemsNames = array();
              foreach ($itemsInCategory as $item) {
                $itemsNames[] = (string) $item -> nazwa;
              }
              return $itemsNames;
            }
    }


  }


  $items = new Items();
  //echo $items->getItem(0) -> id;



  // function isElementInArray ($arr, $elem) {
  //   $n = count($arr);
  //   for ($i=0; $i<$n; $i++) {
  //     if ($arr[$i] == $elem)
  //       return true;
  //   }
  //   return false;
  // }

  //$items = simplexml_load_file("server/xml/oferta.xml");

  if ( isset($_GET["id"]) )
    $itemID = $_GET["id"];
  else
    $itemID = $items -> getItem(0) -> id;

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
          // $categories[0] = (string)$items -> produkt[0] -> $sortBy;

          // foreach ($items as $item) {

          //   if ( count($item -> $sortBy -> children()) > 0 ) {
          //     $subValueName = $item -> $sortBy -> children() -> getName();
          //     foreach ($item -> $sortBy -> children() as $child) {
          //       if ( isElementInArray($categories, $child) )
          //         continue;
          //       else
          //         $categories[] = (string)$child;
          //     }
          //   }

          //   else {
          //     if ( isElementInArray($categories, $item -> $sortBy) )
          //         continue;
          //       else
          //         $categories[] = (string)$item -> $sortBy;
          //   }
          // }


          $categories = $items -> getAllTagValues( $sortBy );
          sort ( $categories );

          $n = count($categories);
          for ($i=0; $i < $n; $i++) {
            echo "<div class='well'>" . $categories[$i] . "</div>";

            // $children = $items -> getItemsObject();
            // $children = $children -> produkt -> $sortBy -> children();

            // if ( @count( $children ) > 0 ) {
              
            //   $itemsInCategory = 
            //     @$items -> getByPath ( "produkt/" . $sortBy . "[" . $sortBy . "='" . $categories[$i] . "']/parent::*" );
            // }
            // else {
            //   //echo "#### 1 #####<br>";
            //   $itemsInCategory = @$items -> getByPath( "produkt[" . $sortBy . "='" . $categories[$i] . "']" );
            // }

            // foreach ($itemsInCategory as $item) {
            //   echo $item -> nazwa . "<br>";
            // }

            $itemsNames = $items -> getItemsNamesByPath("produkt", $sortBy, $categories[$i]);
            print_r($itemsNames);

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

