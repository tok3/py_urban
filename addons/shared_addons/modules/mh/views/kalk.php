					  <?php echo form_open('mh/index/inf');?>
<fieldset>
  <legend>Kalkulatorische Transportkostenermittlung</legend>

  <div class="row hide"> 
	<div class="large-12 columns left">
	  <label>
		<a href="<?php echo $backlink;?>"><i class="fi-arrow-left"></i>&nbsp;Zur&uuml;ck</a>
		<hr>
	  </label>
	</div>

  </div> <!-- ende row -->


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
'Breitendieler Str. 20, 63937 Weilbach'=>'Werk IV Weilbach'
                );

$fd =   isset($_POST['formdata']) ? $_POST['formdata'] : array('location_to' => '');
													   echo form_dropdown('formdata[location_to]', $options, $fd['location_to']);
?>

<!-- 		  <?php echo $formfields['location_to'];?> -->
		</div>


	  </fieldset>
	</div>


  </div> <!-- ende row -->
					<?php echo $info;?>
  
</fieledset>
<button name="submit" value="1" type="submit" class="tiny radius">Berechnen&nbsp;<i class="fi-back size-18">&nbsp;</i></button>
<span style="float:right">
<button name="stck" value="1" type="submit" class="tiny radius">Transportberechnung nach St&uuml;ck&nbsp;<i class="fi-price-tag size-18">&nbsp;</i></button>
<button name="stck" value="1" type="submit" class="tiny radius">Verbindliche Preisanfrage&nbsp;<i class="fi-info size-18">&nbsp;</i></button>
</span>
<?php echo form_close();?>
