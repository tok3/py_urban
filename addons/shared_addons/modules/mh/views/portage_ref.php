<?php echo form_open();?>
<fieldset>
  <legend>Referenz Preise in Euro</legend>

  
  <?php echo $form_errors;?>
  <div class="row"> 
	<div class="large-12 columns left">
	  <label>

		<?php echo $dirSwitch;?>

	  </label>


<!--
<a class="tiny radius secondary button">Speichern&nbsp;{{ asset:image file="mh::weights_ico.png" alt="Icon Image" width="25" }}</a>
<a class="tiny radius secondary button">Speichern&nbsp;{{ asset:image file="mh::dist_ico.png" alt="Icon Image" width="25" }}</a>
-->

	</div>

  </div> <!-- ende row -->

  <div class="row"> 
	<div class="large-12 columns">
	  <?php echo $refMatrix;?>			
	</div>


  </div> <!-- ende row -->

  

<button name="submit" value="1" type="submit" class="tiny radius">Speichern&nbsp;<i class="fi-save size-14">&nbsp;</i></button>
</fieldset>
  <?php echo form_close();?>								   
