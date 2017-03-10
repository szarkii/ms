<?php
	//session_start();
	$CMSPathPHP = $_SERVER['DOCUMENT_ROOT'] . "/server/SzarkiiCMS/";
	$CMSPathHTML = "/server/SzarkiiCMS/";

	include ($CMSPathPHP . "api.php");


	function addDataToNode ( $node, $dataNames, $dataContents ) {
		for ($i=0; $i < count( $dataNames ); $i++) {
			if ( $dataContents[$i] != "" )
				$node->addChild( $dataNames[$i], $dataContents[$i] );
		}
	}

	// function addDataToNode ( $node, $dataNames, $dataContents ) {
	// 	for ($i=0; $i < count( $dataNames ); $i++) {
	// 		if ( $dataContents[$i] == "" )
	// 			continue;

	// 		if ( is_array( $dataContents[$i] ) ) {
	// 			foreach ($dataContents[$i] as $value) {
	// 				$node->addChild( $dataNames[$i], $value );
	// 			}
	// 		}
	// 		else {
	// 			$node->addChild( $dataNames[$i], $dataContents[$i] );
	// 		}
	// 	}
	// }


	$contentFiles = simplexml_load_file( $CMSPathPHP . "contentFiles.xml" );
	if ( isset( $_GET["file"] ) ) {
		$contentFile = $contentFiles->xpath( "file[name='" . $_GET["file"] . "']" );
		$contentFile = $contentFile[0];
	}
	if ( !isset( $_GET["site"] ) ) {
		$_GET["site"] = 1;
	}


	
	if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {

		switch ( $_POST["mode"] ) {
			case "addXML":
				try {
					if ( $contentFiles->xpath( "site[name='" . $_POST["name"] . "']" ) != FALSE ) {
						throw new Exception ( "Plik o tej nazwie już istnieje." );
					}

					# Dodawanie nowej strony do pliku contentFiles
					$contentFile = $contentFiles->addChild( "file" );
					$contentFile->addChild( "name", $_POST["name"] );
					$contentFile->addChild( "path", $_POST["path"] );
					$contentFile->addChild( "template" );

					$contentFiles->asXML( $CMSPathPHP . "contentFiles.xml" );

					# Tworzenie nowego pliku xml
					$str = "<" . $_POST["name"] . "/>";
					$newFile = new SimpleXMLElement( $str );

					$newFile->asXML( $_SERVER['DOCUMENT_ROOT'] . "/" . $_POST["path"] . "/" . $_POST["name"] . ".xml" );

				}
				catch ( Exception $e ) {
					displayError ( $e->getMessage() );
				}

				break;
			



			case "editXML":
				try {
					if ( ($contentFiles->xpath( "file[name='" . $_POST["name"] . "']" ) != FALSE) AND ($_POST["name"] != $_POST["oldName"]) ) {
						throw new Exception ( "Plik o tej nazwie już istnieje." );
					}

					$oldPath = $_SERVER['DOCUMENT_ROOT'] . "/" . $contentFile->path . "/" . $contentFile->name . ".xml";
					$newPath = $_SERVER['DOCUMENT_ROOT'] . "/" . $_POST["path"] . "/" . $_POST["name"] . ".xml";

					if ( file_exists($newPath) ) {
						throw new Exception ( "Pod nową ścieżką znajduje się inny plik." );
					}

					$contentFile->name = $_POST["name"];
					$contentFile->path = $_POST["path"];
					$contentFiles->asXML( $CMSPathPHP . "contentFiles.xml" );
					rename( $oldPath, $newPath );


					$_GET["file"] = $_POST["name"];
					echo "<script>";
					echo "window.open(window.location.pathname + '?site=" . $_GET["site"] . "&file=" . $_POST["name"] . "','_self');";
					echo "</script>";
					exit();

				}
				catch ( Exception $e ) {
					displayError ( $e->getMessage() );
				}
				break;



				case "editTemplate":

					unset( $contentFile->template );
					$template = $contentFile->addChild( "template" );

					addDataToNode ( $template, $_POST["type"], $_POST["name"] );

					$keyTag = $template->xpath("*[.='" . $_POST["key"] . "']");		// wszystkie znaczniki których dla każdej nazwy znacznika wartość jest równa nazwie klucza
					$keyTag = $keyTag[0];
					
					$keyTag->addAttribute("key", "true");


					$contentFiles->asXML( $CMSPathPHP . "contentFiles.xml" );

					break;



				case "removeXML":

					if ( !unlink( $_SERVER['DOCUMENT_ROOT'] . "/" . $contentFile->path . "/" . $contentFile->name . ".xml" ) ) {
						displayError("Nie można usunąć pliku.");
					}
					else {
						unset( $contentFile[0] );
						$contentFiles->asXML( $CMSPathPHP . "contentFiles.xml" );
					}
					unset( $_GET["file"] );
					//header('Location: ' . __FILE__ . "?site=" . $_GET["site"]);
					echo "<script>";
					echo "window.open(window.location.pathname + '?site=" . $_GET["site"] . "','_self');";
					echo "</script>";
					exit();
					

					break;


				case "addElement":
					$path = $_SERVER['DOCUMENT_ROOT'] . "/" . $contentFile->path . "/" . $contentFile->name . ".xml";
					$file = simplexml_load_file( $path );
					$entry = $file->addChild( "entry" );
					
					if ( count( $file ) > 1 ) {
						$id = $file->xpath("entry[last()-1]/id");
						$id = (int) $id[0];
						$id++;
						$entry->addChild( "id", $id );
					}
					else
						$entry->addChild( "id", 0 );

					unset( $_POST["mode"] );
					foreach ( $_POST as $key => $arrValues ) {
						foreach ( $arrValues as $value ) {
							$entry->addChild( $key, $value );
						}
						// if ( count($arrValues) > 1 ) {
						// 	$$entry->addChild($key);
						// }
						// else
						// 	$entry->addChild( $key, $arrValues[0] );
					}

					$file->asXML( $path );


					break;




				case "editElement":
					$path = $_SERVER['DOCUMENT_ROOT'] . "/" . $contentFile->path . "/" . $contentFile->name . ".xml";
					$file = simplexml_load_file( $path );
					$entry = $file->xpath( "entry[id='" . $_GET["eid"] . "']" );
					$entry = $entry[0];

					unset( $entry[0] );

					$entry = $file->addChild( "entry" );
					$entry->addChild( "id", $_GET["eid"] );

					unset( $_POST["mode"] );
					foreach ( $_POST as $key => $arrValues ) {
						foreach ( $arrValues as $value ) {
							$entry->addChild( $key, $value );
						}
					}

					$file->asXML( $path );


					break;



				case "removeElement":
					$path = $_SERVER['DOCUMENT_ROOT'] . "/" . $contentFile->path . "/" . $contentFile->name . ".xml";
					$file = simplexml_load_file( $path );
					$entry = $file->xpath( "entry[id='" . $_GET["eid"] . "']" );
					$entry = $entry[0];

					unset( $entry[0] );

					$file->asXML( $path );

					echo "<script>";
					echo "window.open(window.location.pathname + '?site=" . $_GET["site"] . "&file=" . $_GET["file"] . "','_self');";
					echo "</script>";


					break;


		}
	}

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

	<link rel="stylesheet" href="<?php echo $CMSPathHTML; ?>style.css" >
	<script src="<?php echo $CMSPathHTML; ?>scripts.js"></script>


	<title>Panel Administracyjny</title>
</head>
<body>

	<div class="container">

		<div class="col-sm-3" style="padding-left: 0">

			<div class="panel panel-primary">
				<div class="panel-heading">Ustawienia</div>
				<div class="panel-body">
					<b>Pliki Danych:</b>
					<ul>
						<li><a href="?site=1">Dodawanie</a></li>
						<li><a href='?site=2<?php if (isset($_GET["file"]))echo "&file=" . $_GET["file"]; ?>'>Edycja</a></li>
					</ul>
					<hr>
					<b>Zawartość Plików:</b>
					<ul>
						<li><a href='?site=3<?php if (isset($_GET["file"]))echo "&file=" . $_GET["file"]; ?>'>Dodawanie</a></li>
						<li><a href='?site=4<?php if (isset($_GET["file"]))echo "&file=" . $_GET["file"]; ?>'>Edycja</a></li>
					</ul>

				</div> 
			</div>

		</div>

		<div class="col-sm-9" style="padding: 0">
		<?php

			include( $CMSPathPHP . "/sites//" . $_GET["site"] . ".php" );
 
		?>

		<?php if ( $_GET["site"] != 1 ): ?>
			<div class="panel panel-primary">
				<div class="panel-heading"><b>Wybierz plik do edycji</b></div>
				<div class="panel-body">

					<input type="text" class="searchHideInput" placeholder="wyszukaj plik..." style="margin-left: 0" />
					<div style="position: relative;">
						<?php

						foreach ( $contentFiles as $file ) {
							echo "<a class='searchPosition' href='?site=" . $_GET["site"] . "&file=" . $file->name . "'>" . $file->name . "</a>";
						}

						?>
					</div>

				</div>
			</div>

		<?php endif; ?>

	</div>

</body>

</html>