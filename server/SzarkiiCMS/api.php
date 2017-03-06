<?php

	$templateTags = array(
		array("inputType" => "text", 		"inputName" => "Tekst"),
		array("inputType" => "textArea", 	"inputName" => "Opis"),
		array("inputType" => "date", 		"inputName" => "Data"),
		array("inputType" => "number", 		"inputName" => "Numer")
	);

	function displayError ( $str ) {
		echo "<script>";
		echo "alert('Wystąpił błąd: " . $str . "');";
		echo "</script>";
	}

	

	class XMLSettings {
		private $file;
		private $path;

		public function XMLSettings ( $path ) {
			$this->path = $path;
			$this->file = simplexml_load_file( $path );
		}

		public function getByPath ( $path ) {
			return $file->xpath( $path );
		}

	}

?>