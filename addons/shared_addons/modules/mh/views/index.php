					  <?php echo form_open();?>
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

		  <label>Land:</label> 
		  <?php echo $formfields['country_from'];?>
		</div>


		<div class="large-6 columns">
		  <label>Abgangsort / PLZ:</label> 
		  <?php echo $formfields['location_from'];?>
		</div>


	  </fieldset>
	</div>

	<div class="large-6 columns">
	  <fieldset>
		<legend>Nach</legend>

		<div class="large-6 columns">

		  <label>Land:</label> 
		  <?php echo $formfields['country_to'];?>
		</div>


		<div class="large-6 columns">
		  <label>Empfangsort / PLZ:</label> 
		  <?php echo $formfields['location_to'];?>
		</div>


	  </fieldset>
	</div>


  </div> <!-- ende row -->
					<?php echo $info;?>
  
</fieledset>
<button name="submit" value="1" type="submit" class="tiny radius">Berechnen&nbsp;<i class="fi-refresh size-16">&nbsp;</i></button>

<?php echo form_close();?>
