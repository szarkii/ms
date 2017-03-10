<?php if ( isset( $_GET["file"] ) ): ?>

			<div class="panel panel-primary">

				<div class="panel-heading"><b>Edycja Plików danych</b></div>
				<div class="panel-body">


					<!-- EDYCJA ELEMENTU -->

					Nazwa edytowanego pliku: <b><?php echo $_GET["file"]; ?></b><br><br>

					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#home">Ustawienia</a></li>
						<li><a data-toggle="tab" href="#menu1">Edycja szablonu</a></li>
						<li><a data-toggle="tab" href="#menu2">Usuń plik</a></li>
					</ul>

					<div class="tab-content" style="border: solid #ddd 1px; border-top: none; padding: 20px">
						<div id="home" class="tab-pane fade in active">
							
						<form action='' method='POST'>
							<div class='form-block'>
								<input type='hidden' name='mode' value='editXML' />
								<input type='hidden' name='oldName' value='<?php echo $contentFile->name; ?>' />

								<span class='input-title'>Ścieżka:</span><br/>
								<input type='text' name='path' value='<?php echo $contentFile->path; ?>' /><br>

								<span class='input-title'>Nazwa pliku:</span><br/>
								<input type='text' name='name' value='<?php echo $contentFile->name ; ?>' /><br>


								<button type='submit'>Zatwierdź</button>

							</div>							
						</form>

						</div>

						<!-- EDYCJA SZABLONU -->
						<div id="menu1" class="tab-pane fade">
							

							<form action='' method='POST'>
								<div class='form-block'>
									<input type='hidden' name='mode' value='editTemplate' />

									<span style="margin-left: 30px">klucz</span>

									<?php
									$template = $contentFile->template;
									$template = $template[0];
									if ( empty( $template->children() ) ) {
										$template->addChild("text");
										$keyField = "";
									}
									else {
										$keyField = $template->xpath("*[@key='true']");
										$keyField = (string) $keyField[0];
									}			
									// $template = $template[0]; // <-- wcześniej ta linijka była tu...

									
									$i2 = 0;
									?>
									<?php foreach ($template as $field): ?>

									<div style="display: block; padding: 8px;">
										<input style='margin-right: 20px;' type='radio' name='key' value='<?php echo $i2++; ?>' <?php if ($field == $keyField) { echo " checked"; $isFirst = false; } ?> />

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
									<span class='form-addField iconSM glyphicon glyphicon-plus-sign' title='dodaj pole'></span>
									<span class='form-removeField iconSM glyphicon glyphicon-remove' title='usuń pole'></span>
									</div>
									<?php endforeach; ?>

									<button type='submit' style="display: inline-block; margin-top: 20px">Zatwierdź</button>
									<label><input type='checkbox' class="form-activeBtns" /> Włącz możliwość usuwania pól</label>

								</div>
							</form>


						</div>


						<!-- USUWANIE ELEMENTU -->
						<div id="menu2" class="tab-pane fade">
							<center>
							Naciśnięcie poniższego przycisku spowoduje usunięcie Pliku Danych wraz <u>z całą zawartością</u>.
							<form action='<?php echo "?site=" . $_GET["site"] . "&file=" . $_GET["file"]; ?>' method='POST'>
								<input type='hidden' name='mode' value='removeXML' />
								<input type="submit" value="Usuń Plik">
							</form>
							</center>
						</div>
					</div>
				</div>
			</div>
<?php endif ?>