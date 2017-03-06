<?php
if ( isset( $_GET["file"] ) )
	$file = simplexml_load_file( $_SERVER['DOCUMENT_ROOT'] . "/" . $contentFile->path . "/" . $contentFile->name . ".xml" ); 
?>

<?php if ( isset( $_GET["file"] ) AND isset( $_GET["eid"] ) ): ?>

		<?php
			$template = $contentFile->template;
			$template = $template[0];

			$entry = $file->xpath("entry[id='" . $_GET["eid"] . "']");
			$entry = $entry[0];
		?>
	

		<div class="panel panel-primary">
			<div class="panel-heading">Edycja Elementu</b></div>
			<div class="panel-body">


				Nazwa edytowanego pliku: <b><?php echo $_GET["file"]; ?></b><br>
				Klucz edytowanego elementu: <b><?php echo $entry->nazwa . " [id: " . $entry->id . "]"; ?></b><br><br>

				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#home">Edycja elementu</a></li>
					<li><a data-toggle="tab" href="#menu1">Usuwanie elementu</a></li>
				</ul>

				<div class="tab-content" style="border: solid #ddd 1px; border-top: none; padding: 20px">
					<div id="home" class="tab-pane fade in active">

						<form action='' method='POST'>
							<div class='form-block'>
								<input type='hidden' name='mode' value='editElement' />
								
								<?php foreach ($template as $field): ?>
									<span class='input-title'><?php echo $field; ?>:</span>

									<?php foreach ($entry->xpath( $field ) as $fieldData): ?>
									<div style='display: inline-block;'>

										<input type='<?php echo $field->getName(); ?>' name='<?php echo $field; ?>[]' placeholder="<?php echo $field; ?>..." value='<?php echo $fieldData; ?>' />

										<span class='form-addField iconSM glyphicon glyphicon-plus-sign' title='dodaj pole'></span>
										<span class='form-removeField iconSM glyphicon glyphicon-remove' title='usuń pole'></span>

									</div>
									<?php endforeach; ?>

								<?php endforeach; ?>

								<span style="display: block;"></span>
								<button type='submit' style="display: inline-block; margin-top: 20px">Zatwierdź</button>
								<label><input type='checkbox' class="form-activeBtns" /> Włącz możliwość usuwania pól</label>
							</div>
						</form>

					

					</div>

					<div id="menu1" class="tab-pane fade">
						
						<center>
							Naciśnięcie poniższego przycisku spowoduje usunięcie elementu <u>z całą zawartością</u>.
							<form action='' method='POST'>
								<input type='hidden' name='mode' value='removeElement' />
								<input type="submit" value="Usuń Plik">
							</form>
						</center>						


					</div>
				</div>
					
			</div>
		</div>
<?php endif ?>

<?php if ( isset( $_GET["file"] ) ): ?>
		<div class="panel panel-primary">
			<div class="panel-heading"><b>Wybierz element do edycji</b></div>
			<div class="panel-body">
				<ul>
					<?php
					//print_r($file);

					foreach ( $file as $entry ) {
						echo "<li><a href='?site=" . $_GET["site"] . "&file=" . $_GET["file"] . "&eid=" . $entry->id . "'>";
						echo $entry->nazwa . "</a></li>";
					}

					?>
				</ul>
			</div>
		</div>
<?php endif ?>