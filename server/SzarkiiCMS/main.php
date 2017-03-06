<?php
	//session_start();
	$CMSPathPHP = $_SERVER['DOCUMENT_ROOT'] . "/server/SzarkiiCMS/";
	$CMSPathHTML = "/server/SzarkiiCMS/";

	include ($CMSPathPHP . "api.php");


	$sites = simplexml_load_file( $CMSPathPHP . "sites.xml" );

	$activeTab1 = "";
	$activeTab2 = "";
	$activeTab3 = "";
	if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {

		switch ( $_POST["mode"] ) {
			case "addXML":
				try {
					if ( $sites->xpath( "site[name='" . $_POST["name"] . "']" ) != FALSE ) {
						throw new Exception ( "Plik o tej nazwie już istnieje." );
					}

					# Dodawanie nowej strony do pliku sites
					$site = $sites->addChild( "site" );
					$site->addChild( "name", $_POST["name"] );
					$site->addChild( "path", $_POST["path"] );
					$site->addChild( "template" );

					$sites->asXML( $CMSPathPHP . "sites.xml" );

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
					if ( ($sites->xpath( "site[name='" . $_POST["name"] . "']" ) != FALSE) AND ($_POST["name"] != $_POST["oldName"]) ) {
						throw new Exception ( "Plik o tej nazwie już istnieje." );
					}

					$site = $sites->xpath( "site[name='" . $_GET["element"] . "']" );
					$site = $site[0];

					$oldPath = $_SERVER['DOCUMENT_ROOT'] . "/" . $site->path . "/" . $site->name . ".xml";
					$newPath = $_SERVER['DOCUMENT_ROOT'] . "/" . $_POST["path"] . "/" . $_POST["name"] . ".xml";

					if ( file_exists($newPath) ) {
						throw new Exception ( "Pod nową ścieżką znajduje się inny plik." );
					}

					$site->name = $_POST["name"];
					$site->path = $_POST["path"];
					$sites->asXML( $CMSPathPHP . "sites.xml" );
					rename( $oldPath, $newPath );


					$_GET["element"] = $_POST["name"];
					echo "<script>";
					echo "window.open(window.location.pathname + '?site=" . $_GET["site"] . "&element=" . $_POST["name"] . "','_self');";
					echo "</script>";
					exit();

				}
				catch ( Exception $e ) {
					displayError ( $e->getMessage() );
				}
				break;



				case "editTemplate":
					$template = $sites->xpath( "site[name='" . $_GET["element"] . "']" );
					$template = $template[0]->template;

					for ($i=0; $i < count( $_POST["type"] ); $i++) {
						$template->addChild( $_POST["type"][$i], $_POST["name"][$i] );
						//echo "typ: " . $_POST["type"][$i] . ", nazwa: " . $_POST["name"][$i] . "<br>";
					}

					$sites->asXML( $CMSPathPHP . "sites.xml" );

					break;



				case "removeXML":
					
					$site = $sites->xpath( "site[name='" . $_GET["element"] . "']" );
					$site = $site[0];

					if ( !unlink( $_SERVER['DOCUMENT_ROOT'] . "/" . $site->path . "/" . $site->name . ".xml" ) ) {
						displayError("Nie można usunąć pliku.");
					}
					else {
						unset( $site[0] );
						$sites->asXML( $CMSPathPHP . "sites.xml" );
					}
					unset( $_GET["element"] );
					//header('Location: ' . __FILE__ . "?site=" . $_GET["site"]);
					echo "<script>";
					echo "window.open(window.location.pathname + '?site=" . $_GET["site"] . "','_self');";
					echo "</script>";
					exit();
					

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

  <link rel="stylesheet" href="style/style.css" >
  <link rel="stylesheet" href="<?php echo $CMSPathHTML; ?>style.css" >
  <script src="<?php echo $CMSPathHTML; ?>scripts.js"></script>


  <title>Oferta - Multi Service</title>
</head>
<body>

	<div class="container">

		<div class="col-sm-3" style="padding-left: 0">

			<div class="panel panel-primary">
				<div class="panel-heading">Ustawienia</b></div>
				<div class="panel-body">
					<ul> 
						<li><a href="?site=1">Dodawanie Pliku Danych</a></li>
						<li><a href="?site=2">Edycja Pliku Danych</a></li>
						<li><a href="?site=3">Edycja zawartości Pliku Danych</a></li>
					</ul>
				</div> 
			</div>

		</div>

		<div class="col-sm-9" style="padding: 0">

			<div class="panel panel-primary">

				<?php if ( $_GET["site"] == 1 ): ?>

				<div class="panel-heading">Dodawanie Plików danych</b></div>
				<div class="panel-body">

					<form action='' method='POST'>
						<div class='form-block'>
							<input type='hidden' name='mode' value='addXML' />

							<span class='input-title'>Ścieżka do katalogu w którym zostanie utworzony plik:</span><br/>
							<input type='text' name='path' value="server/xml" /><br>

							<span class='input-title'>Nazwa pliku:</span><br/>
							<input type='text' name='name' placeholder='Podaj nazwę...' /><br>

							<button type='submit'>Zatwierdź</button>
							<button type='button' class='textAreaSubmit'>Zatwierdź zmiany</button>
						</div>
					</form>

						
				</div>

				<?php elseif ( $_GET["site"] == 2 ): ?>

				<div class="panel-heading">Edycja Plików danych</b></div>
				<div class="panel-body">


					<!-- EDYCJA ELEMENTU -->
					<?php if ( isset( $_GET["element"] ) ): ?>
					<?php 
						$site = $sites->xpath( "site[name='" . $_GET["element"] . "']" );
						$site = $site[0];
					?>

					Nazwa edytowanego pliku: <b><?php echo $_GET["element"]; ?></b><br><br>

					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#home">Ustawienia</a></li>
						<li><a data-toggle="tab" href="#menu1">Edycja szablonu</a></li>
						<li><a data-toggle="tab" href="#menu2">Usuń plik</a></li>
					</ul>

					<div class="tab-content" style="border: solid #ddd 1px; border-top: none; padding: 20px">
						<div id="home" class="tab-pane fade in active">
							
						<form action='<?php echo "?site=" . $_GET["site"] . "&element=" . $_GET["element"]; ?>' method='POST'>
							<div class='form-block'>
								<input type='hidden' name='mode' value='editXML' />
								<input type='hidden' name='oldName' value='<?php echo $site->name; ?>' />

								<span class='input-title'>Ścieżka:</span><br/>
								<input type='text' name='path' value='<?php echo $site->path; ?>' /><br>

								<span class='input-title'>Nazwa pliku:</span><br/>
								<input type='text' name='name' value='<?php echo $site->name ; ?>' /><br>


								<button type='submit'>Zatwierdź</button>

							</div>							
						</form>

						</div>

						<!-- EDYCJA SZABLONU -->
						<div id="menu1" class="tab-pane fade">
							

							<form action='<?php echo "?site=" . $_GET["site"] . "&element=" . $_GET["element"]; ?>' method='POST'>
								<div class='form-block'>
									<input type='hidden' name='mode' value='editTemplate' />

									<?php
									$template = $site->template;
									//print_r($template);
									if ( empty( $template->children() ) ) {
										$template->addChild("text");
									}					
									$template = $template[0];
									?>

									<?php foreach ($template as $field): ?>

									<div style="display: block; padding: 8px;">
										<select name='type[]'>
											<?php
											for ($i=0; $i < count($templateTags); $i++) {
												echo "<option value='" . $templateTags[$i]["inputType"] . "'";
												
												if ( $templateTags[$i]["inputType"] == $field->getName() )
													echo " selected";

												echo ">" . $templateTags[$i]["inputName"] . "</option>";
											}
											?>
											<input type='text' name='name[]' value='<?php echo $field; ?>' placeholder='nazwa pola...' />
										</select>
									<span class='template-addField iconSM glyphicon glyphicon-plus-sign' title='dodaj pole'></span>
									<span class='template-removeField iconSM glyphicon glyphicon-remove' title='usuń pole'></span>
									</div>
									<?php endforeach; ?>

									<button type='submit'>Zatwierdź</button>

								</div>
							</form>


						</div>


						<!-- USUWANIE ELEMENTU -->
						<div id="menu2" class="tab-pane fade">
							<center>
							Naciśnięcie poniższego przycisku spowoduje usunięcie Pliku Danych wraz <u>z całą zawartością</u>.
							<form action='<?php echo "?site=" . $_GET["site"] . "&element=" . $_GET["element"]; ?>' method='POST'>
								<input type='hidden' name='mode' value='removeXML' />
								<input type="submit" value="Usuń Plik">
							</form>
							</center>
						</div>
					</div>

					<!-- WYBÓR ELEMENTU -->
					<?php endif; ?>
					<br><br>
					<ul>
					<?php

					//$sites = simplexml_load_file( $CMSPathPHP . "sites.xml" );

					foreach ( $sites as $site ) {
						echo "<li><a href='?site=" . $_GET["site"] . "&element=" . $site->name . "'>" . $site->name . "</a></li>";
					}

					?>
					</ul>

				</div>


				<?php else: ?>

				3cia opcja

				<?php endif; ?>



			</div>

		</div>
	</div>

</body>

</html>