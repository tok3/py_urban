<?php echo $fields['open'];?>
  <fieldset>
	<legend>Geichwicht bis in KG</legend>


					  <?php echo $form_errors;?>
	<div class="row"> 
	  <div class="large-6 columns left">
	<label><a href="<?php echo $backlink;?>"><i class="fi-arrow-left"></i>&nbsp;Zur&uuml;ck</a>
<hr>
</label>
	  </div>

	</div> <!-- ende row -->

	<div class="row"> 
	  <div class="large-3 columns">
	 <label>KG</label> 
		<?php echo $fields['kg'];?>
	  </div>

	  <div class="large-3 columns left">
	  </div>

	  <div class="large-3 columns left">
	  </div>

	</div> <!-- ende row -->

							 </fieledset>
	  <?php echo $fields['delete'];?>
<?php echo $fields['submit'];?>
<?php echo $fields['close'];?>
