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
	  <div class="large-2 columns">
		<label>Von:</label> 
LKZ
	  </div>
					<div class="large-4 columns">
			   <select><option>select</option></select>
	  </div>



	</div>

	<div class="large-6 columns">
	  <div class="large-3 columns">
		<label>Nach:</label> 

	  </div>

	</div>

  </div> <!-- ende row -->
								   
</fieledset>
<button name="submit" value="1" type="submit" class="tiny radius">Speichern&nbsp;<i class="fi-save size-14">&nbsp;</i></button>

<?php echo form_close();?>
