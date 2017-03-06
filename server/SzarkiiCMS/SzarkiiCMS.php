<?php

	$SzarkiiCMSPath = "server/SzarkiiCMS/";


	/******** INCLUDE ********/
	include_once("bootstrap.php");
	echo "<script src='" . $SzarkiiCMSPath ."TextArea.js'></script>";
	echo "<script src='" . $SzarkiiCMSPath ."scripts.js'></script>";
	echo "<link rel='stylesheet' href='" . $SzarkiiCMSPath . "style.css'>";

	
	/****** PATHS ********/
	//$TextAreaPath = "./TextArea.php";


	class XMLObject {
		private $object;
		private $path;
		private $templatePath;
		private $TextAreaPath = "TextArea.php";

		public function XMLObject ( $path ) {
			$this->path = $path;

			$this->templatePath = 
				pathinfo($this->path,  PATHINFO_DIRNAME ) . "/" . pathinfo($this->path,  PATHINFO_FILENAME ) . "-template." . pathinfo($this->path,  PATHINFO_EXTENSION );

			$this->object = simplexml_load_file( $path );
		}

		public function hasChild ( $object ) {
			$children = $object -> children();
			if ( @count( $children ) > 0 )
				return true;
			return false;
		}

		public function getElementTags ( $id, $exceptions = array() ) {
			$tags = array();

			$objectChildren = $this->object -> children();

			foreach ($objectChildren[$id] as $tag) {
				$tagName = (String) $tag -> getName();
				$tagMode = (String) $tag["mode"];
				//$attrs = $tag -> attributes();
				//$tagMode = (String) $attrs["mode"];
				//echo "$tagMode" . "<br>";

				if ( !in_array( $tagName , $exceptions ) ) {
					//echo $tagName . ": " . $tag . "<br>";
					if ( $this -> hasChild( $tag ) ) {
						foreach ($tag as $child) {
							$tmp[] = (String) $child;
						}
						$tags[] = array("name" => $tagName, "mode" => $tagMode, "value" => $tmp);
					}
					else 
						$tags[] = array("name" => $tagName, "mode" => $tagMode, "value" => (String) $tag);
				}
					
			}

			return $tags;

		}

		public function printAsInputs ( $object, $exceptions = array() ) {
			foreach ($object as $tag) {
				$tagName = (String) $tag -> getName();
				$tagMode = (String) $tag["mode"];

				if ( !in_array( $tagName , $exceptions ) ) {
					if ( empty( $tag ) ) {
						$tagValues[0] = "";
					}
					else {
						if ( $this -> hasChild( $tag ) ) {
							foreach ($tag as $child) {
								$tagValues[] = (String) $child;
							}
						}
						else 
							$tagValues[0] = (String) $tag;
					}
				}


				echo "<br/><span class='form-removeTag iconSM glyphicon glyphicon-remove' title='usuń znacznik'></span>";
				echo "<span class='input-title'>$tagName:</span><br/>";

				$deletefield = "<span class='form-removeField iconSM glyphicon glyphicon-remove' title='usuń pole'></span>";
				$addfield = "<span class='form-addField iconSM glyphicon glyphicon-plus-sign' title='dodaj pole'></span>";
				

				switch ( $tagMode ) {
					case "description":
						//include_once( $this->TextAreaPath );
						
						for ( $i = 0; $i < count( $tagValues ); $i++ ) {
							echo 
							"<div class='textArea' name='" . $tagName . "[]' contenteditable>" . $tagValues[$i] . "</div>";
							echo $deletefield;
						}
						echo $addfield;

						break;
					
					default:
						for ( $i = 0; $i < count( $tagValues ); $i++ ) {
							echo 
							"<input type='$tagMode' name='" . $tagName . "[]' placeholder='$tagName...' value='" .
							$tagValues[$i] . "' />";
							echo $deletefield;
						}
						echo $addfield; 

						break;
				}



			}
		}

		public function printFormTagEdition () {

			echo "<div class='dropdown'>";
				echo "<button class='btn btn-primary dropdown-toggle' type='button' data-toggle='dropdown'>Dodaj Znacznik ";
				echo "<span class='caret'></span></button>";
				echo "<ul class='dropdown-menu'>";
				echo "<li><a href='javascript:void(0)' class='form-addTag' inputType='text'>Tekst</a></li>";
				echo "<li><a href='javascript:void(0)' class='form-addTag' inputType='textArea'>Opis</a></li>";
				echo "<li><a href='javascript:void(0)' class='form-addTag' inputType='date'>Data</a></li>";
				echo "<li><a href='javascript:void(0)' class='form-addTag' inputType='number'>Numer</a></li>";
				echo "</ul>";
			echo "</div>";
		}

		public function printFormAdd ( $templatePath ) {
			echo "<form action='' method='POST'>";
				echo "<div class='form-block'>";
					echo "<input type='hidden' name='mode' value='edit' />";
					include( $this->TextAreaPath );

					if ( file_exists($templatePath) ) {
						$template = simplexml_load_file ( $templatePath );
						$this -> printAsInputs( $template -> children() );
					}

					echo "<br>";
					$this->printFormTagEdition();
					echo "<br>";
				

					echo "<button type='button' class='textAreaSubmit'>Zatwierdź zmiany</button>";
				echo "</div>";
			echo "</form>";
		}

	}


	if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
		$xml = simplexml_load_file( "/tst.xml" );
		if ( $_POST["mode"] == "edit" ) {
			unset($_POST["mode"]);
			foreach ($_POST as $key => $post) {
				foreach ($post as $value) {
					echo "$key: $value<br>";
				}
			}
		}
	}


?>