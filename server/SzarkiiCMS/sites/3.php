<?php if ( isset( $_GET["file"] ) ): ?>
		<div class="panel panel-primary">
			<div class="panel-heading">Dodawanie Elementu</b></div>
			<div class="panel-body">

				<?php
					$template = $contentFile->template;
					$template = $template[0];
				?>

				<form action='' method='POST'>
					<div class='form-block'>
						<input type='hidden' name='mode' value='addElement' />
						
						<?php foreach ($template as $field): ?>
							<span class='input-title'><?php echo $field; ?>:</span>
							<div style='display: inline-block;'>
								<input type='<?php echo $field->getName(); ?>' name='<?php echo $field; ?>[]' placeholder="<?php echo $field; ?>..." />
								<span class='form-addField iconSM glyphicon glyphicon-plus-sign' title='dodaj pole'></span>
								<span class='form-removeField iconSM glyphicon glyphicon-remove' title='usuń pole'></span>
							</div>

						<?php endforeach; ?>

						<span style="display: block;"></span>
						<button type='submit' style="display: inline-block; margin-top: 20px">Zatwierdź</button>
						<label><input type='checkbox' class="form-activeBtns" /> Włącz możliwość usuwania pól</label>
					</div>
				</form>

					
			</div>
		</div>
<?php endif ?>