

<div class="row"> 

  <div class="large-12 columns">
	<fieldset>
	  <legend>Kalkulation</legend>

	  <div class="row"> 
<?php
     if(!$this->input->post('man_dist'))
     {
     ?>

		<div class="large-3 columns">
		  <label>Von:</label> 
		  <?php echo $vc_from;?>

		</div>


		<Div class="large-3 columns left">
		  <label>Nach:</label> 
		  <?php echo $vc_to;?>
		</div>
<?php }else{ ?>
		<div class="large-6 columns left">
     &nbsp;
     </div>

<?php } ?>

     <div class="large-3 columns left">
		  <p>
			<label>Entfernung:</label> 
			<strong><?php echo $distance->text;?></strong>
		  </p>
		  <p>
			<label>Transporkosten f&uuml;r <?php echo $post_fields['weight'];?> Kg:</label> 
			<strong><?php echo $this->format->displCurr($price->portage_eur);?> &euro;</Strong>
		  </p>
		</div>

	  </div>


	</fieldset>
  </div>




</div><!-- ende row -->

