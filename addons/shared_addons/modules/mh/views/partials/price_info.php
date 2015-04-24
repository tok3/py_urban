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
<label>Transporkosten&nbspf&uuml;r&nbsp<?php echo $this->format->displCurr($post_fields['weight']);?>&nbsp;<?php echo lang('mh:' . $this->input->post('unit'));?>:</label> 
	    <strong><?php echo $this->format->displCurr($price->portage_eur);?> &euro;</Strong>
	  </p>
	</div>


	<!-- dev -->
	<div class="row">
                                         
	  <div class="large-6 columns">
                                         &nbsp;
                                         <!--
                                         <input type="radio" name="kalkArt" value="vpe" id="artVPE" checked="checked" ><label for="artVPE">Mengeneinheit</label>
      <input type="radio" name="kalkArt" value="unt" id="artUnt"><label for="artUnt">Exact</label>
  -->
                                         </div>

	  <div class="large-3 columns left" >
	    <p  id="mePreis">
	      <label>Kalkulatorischer Transportpreis (Anz. Mengeneinheit):</label> 

	      <input type="number" min="0" name="formdata[mnt_unit]" value="<?php echo $post_fields['mnt_unit'];?>">
	      </p>
	    <p id="exaterPreis" class="_hide">
                                                                                                               <label>Individuelle St&uuml;ckzahl:</label> 

	      <input type="number" min="0" name="formdata[exact_unit]" value="<?php echo $post_fields['exact_unit'];?>">
<?
                                         if(isset($exactPrice)){
            ?>
                                         <strong class="hide"><?php echo $this->format->displCurr($exactPrice);?> &euro;</Strong>
<?
                                         }
            ?>
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




