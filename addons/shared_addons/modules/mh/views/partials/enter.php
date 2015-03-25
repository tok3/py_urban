

  <div class="row"> 
	<div class="large-6 columns">
	  <fieldset>
		<legend>Gewicht</legend>

		<div class="large-6 columns">

		  <label>Kg: *</label> 
		  <?php echo $formfields['weight'];?>
		</div>


	  </fieldset>


	</div>
	<div class="large-6 columns">

		<div class="large-12 columns">
																						<label>&nbsp;</label> 

		  <?php echo $errors;?>
		</div>

	</div>

  </div>

  <div class="row"> 
	<div class="large-6 columns">
	  <fieldset>
		<legend>Von</legend>

		<div class="large-6 columns">

		  <label>Land: *</label> 
		  <?php echo $formfields['country_from'];?>
		</div>


		<div class="large-6 columns">
		  <label>Abgangsort / PLZ: *</label > 
		  <?php echo $formfields['location_from'];?>
		</div>


	  </fieldset>
	</div>

	<div class="large-6 columns">
	  <fieldset>
		<legend>Nach</legend>

		<div class="large-6 columns">

		  <label>Land:</label> 
<!-- 		  <?php echo $formfields['country_to'];?> -->
<select name="formdata[country_to]">
<option value="DE" selected="selected">Deutschland (DE)</option>
</select>
		</div>


		<div class="large-6 columns">
		  <label>Empfangsort: *</label> 
<?

$options = array(
 ''=>'Bitte w&auml;hlen',
' , 63741 Nilkheim '=>'Werk II Aschaffenburg-Nilkheim',
'Carl-von-Linde-Platz, 63743 Aschaffenburg'=>'Werk I Aschaffenburg-Schweinheim - Zentrale',
'Hanauer Landstr. 100, 63796 Kahl'=>'Werk III Kahl',
 'Breitendieler Str. 20, 63937 Weilbach'=>'Werk IV Weilbach',
 'Berzelius Str. 10, 22113 Hamburg'=>'Werk Hamburg'
                );

$fd =   isset($_POST['formdata']) ? $_POST['formdata'] : array('location_to' => '');
													   echo form_dropdown('formdata[location_to]', $options, $fd['location_to']);
?>

<!-- 		  <?php echo $formfields['location_to'];?> -->
		</div>


	  </fieldset>
	</div>


  </div> <!-- ende row -->
