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
    public function checkId ($id) {
      if ( $id >= 0 AND $id < count($this->items) )
        return true;
      return false;
    }
    public function getById ($id) {
      if ( $this->checkId($id) )
        return $this->items -> produkt[$id];
      else
        //return 0;
        echo "Error: Index Out of Range";
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
            if ( !in_array($child, $allTagValues) AND $child != NULL )
              $allTagValues[] = $child;
          }
        }
        else {
          $value = (string) $item -> $tagName;
          if ( !in_array( $value, $allTagValues) AND $value != NULL )
            $allTagValues[] = $value;
        }
      }
      return $allTagValues;
    }
    public function getByPath ( $path ) {
      return $this->items -> xpath($path);
    }
    public function getObjectsByXpath ( $path, $tagName, $tagValue ) {
      /****** Ważne założenie: jeśli objekt ma dzieci, to dzieci muszą mieć tą samą nazwę znacznika  ******/
      $children = $this->items -> produkt -> $tagName -> children();
      if ( @count( $children ) > 0 ) {
        $objects = 
          $this->items -> xpath("produkt/" . $tagName . "[" . $tagName . "='" . $tagValue . "']/parent::*");
      }
      else {
        $objects = $this->items -> xpath( "produkt[" . $tagName . "='" . $tagValue . "']" );
      }
      if ($objects != NULL) {
        // $itemsNames = array();
        // foreach ($objects as $item) {
        //   $itemsNames[] = (string) $item -> nazwa;
        // }
        return $objects;
      }
    }
  }
  $items = new Items();
  
  if ( isset($_GET["id"]) ) {
    if ( $items -> checkId( $_GET["id"] ) )
      $itemID = (Integer) $_GET["id"];
    else
      $itemID = 0;
  }
  else
    $itemID = (Integer) $items -> getById(0) -> id;
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
      <div class="panel-heading">Tkaniny - <b><?php echo $sortBy ?></b></div>
      <div class="panel-body">

        <div class="form-group">
          <label for="sel1">Grupuj tkaniny według:</label>
          <form action="" method="GET">
            <select class="form-control" onchange="this.form.submit()" name="sort">
            <?php
              $i = 0;
              $groups = array();
              while ( $items -> checkId($i) ) {
                $item = $items -> getById($i);
                foreach ($item -> children() as $children) {
                  $children = (String) $children -> getName();
                  if ( !in_array($children, $groups) ) {
                    $groups[] = $children;
                  }
                }
                $i++;
              }
              
              foreach ($groups as $name) {
                if ($name == $sortBy)
                  echo "<option value='$name' selected>$name</option>";
                else
                  echo "<option value='$name'>$name</option>";
              }
              
            ?>
            </select>
          </form>
        </div>

        <hr/>


        <?php
        
          $categories = $items -> getAllTagValues( $sortBy );
          sort ( $categories );
          foreach ($categories as $category) {
            echo "<div class='well'>" . $category . "</div>";
            $itemsInCategory = $items -> getObjectsByXpath("produkt", $sortBy, $category);
            foreach ($itemsInCategory as $item) {
              echo "<a href='oferta.php?sort=$sortBy&id=" . $item->id . "'>" . $item -> nazwa . "</a><br>";
              //echo $item -> nazwa . " [" . $item -> id . "]<br>";
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
 
          $currentItem = $items -> getById( $itemID );
          foreach ($currentItem as $item) {
            echo $item -> getName() . ": " . $item . "<br>";
          }
        ?>
      </div>
    </div>
  
  </div>

</div>

  <?php include ("server/php/bottom.php"); ?>

</body>
</html>