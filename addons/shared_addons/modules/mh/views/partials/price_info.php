<style>
#tablePPU
{
    width:100%;
}
#tablePPU TD
{
    text-align:right;
}
#tablePPU TH
{
    text-align:right;
}
</style>
<?php
if($this->input->post('man_dist'))
{
    echo form_hidden('man_dist',$this->input->post('man_dist'));

}

?>





<div class="row"> 

  <div class="large-12 columns">

    <Fieldset>
      <legend>Kalkulation</legend>

      <div class="large-6 columns"> <!-- left col -->

      <div class="row"> 

<?php
     if(!$this->input->post('man_dist'))
     {
     ?>

		<div class="large-6 columns">
		  <label>Von:</label> 
		  <?php echo $vc_from;?>

		</div>
<?php }else{ ?>
		<div class="large-6 columns">
		  <label>Von:</label>
             <ul class="vcard" style="width:99%">
				   <li class="">

             &nbsp;
</li>

             <li class="locality">
<?php echo $post_fields['country_from_long'];?>
</li>

             </ul>
		</div>
<?php } ?>


		<div class="large-6 columns left">
		  <label>Nach:</label> 
		  <?php echo $vc_to;?>
		</div>

             </div> <!-- /row -->

                  <div class="row"> 
<div class="large-12 columns">
		  <?php echo $cost_per_unit;?>


             </div> <!-- /columns -->
             </div> <!-- /row -->

             </div> <!-- /left col -->





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


	<!-- dev -->
	<div class="row"> 
	  <div class="large-6 columns">
	    &nbsp;
	  </div>

	  <div class="large-3 columns left" >
	    <p>
	      <label>Kalkulatorischer Transportpreis (Mengeneinheit pro Kg):</label> 

	      <input type="text" name="formdata[mnt_unit]" value="<?php echo $post_fields['mnt_unit'];?>">
	      </p>
	    </div>

	  </div> <!-- /columns -->
	</div> <!-- /row -->
	<!-- /dev  -->


                                         
      </fieldset>
                                         <a href="mh/index" class="button tiny radius success"><i class="fi-arrow-left size-18">&nbsp;</i>&nbsp;Zur&uuml;ck / Neue Eingabe</a>
<span style="float:right">
<button name="submit" value="1" type="submit" class="tiny radius">Transportberechnung nach St&uuml;ck&nbsp;<i class="fi-price-tag size-18">&nbsp;</i></button>
<a href="<?php echo site_url($this->router->fetch_module().'/mailkram');?>"name="stck" class="button tiny radius">Verbindliche Preisanfrage&nbsp;<i class="fi-info size-18">&nbsp;</i></a>
    </div> <!-- /columns -->
  </div> <!-- /row -->




