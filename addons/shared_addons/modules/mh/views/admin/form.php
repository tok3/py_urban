<section class="title">
	<!-- We'll use $this->method to switch between ramstrg.create & ramstrg.edit -->
	<h4><?php echo lang('ramstrg:'.$this->method); ?></h4>
</section>



<section class="item">
	<div class="content">

		<?php 

		echo '<script> var formMode = "'.$this->uri->segment('3').'";</script>';

		echo form_open_multipart($this->uri->uri_string(), 'class="crud" id="businessHours"'); ?>

		
		<div class="tabs">
			<ul class="tab-menu">
				<li><a href="#sites-panel-details"><span>Details</span></a></li>

				<li><a href="#sites-panel-business-hours"><span><?php echo lang('ramstrg:business_hours'); ?></span></a></li>
				<li><a href="#sites-panel-holidays"><span><?php echo lang('ramstrg:holidays'); ?></span></a></li>
			</ul>
			<div  class="form_inputs" id="sites-panel-details">
				<!-- details--> 
				<fieldset>
					<ul>
						<li class="<?php echo alternator('', 'even'); ?>">
							<label for="name"><?php echo lang('ramstrg:name'); ?> <span>*</span></label>
							<div class="input"><?php echo form_input('name', set_value('name', $ramstrg->name), 'class="width-20"'); ?>
								<?php echo form_hidden('slug', set_value('slug', $ramstrg->slug)); ?>
							</div>
						</li>

						<li class="<?php echo alternator('', 'even'); ?>">
							<label for="str"><?php echo lang('ramstrg:str_nr'); ?> <span>*</span></label>
							<div class=""><?php echo form_input('str', set_value('str', $ramstrg->str), 'class="width-20"'); ?>
								<?php echo form_input('nr', set_value('nr', $ramstrg->nr), 'class="width-5"'); ?>
							</div>
						</li>


						<li class="<?php echo alternator('', 'even'); ?>">
							<label for="plz"><?php echo lang('ramstrg:plz'); ?> / <?php echo lang('ramstrg:ort'); ?><span>*</span></label>
							<div class=""><?php echo form_input('plz', set_value('plz', $ramstrg->plz), 'class="width-5 inline"'); ?>
								<?php echo form_input('ort', set_value('ort', $ramstrg->ort), 'class="width-15"'); ?>
							</div>
						</li>


					</ul>
				</fieldset>
				<!-- ende details -->
			</div> 
			<div  class="form_inputs" id="sites-panel-business-hours">
				<!-- öffnungszeiten --> 
				<fieldset>


					<?php if (!empty($business_hours)): ?>

					<table>
						<thead>
							<tr>
								<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
								<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:from'); ?></th>
								<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:to'); ?></th>
								<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:from'); ?></th>
								<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:to'); ?></th>
								
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5">
									<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
								</td>
							</tr>
						</tfoot>
						<tbody>

							<?php 
							$day_names = lang('ramstrg:day_names');
							foreach( $bh_inputs as $item ): ?>
							<tr>
								<td><?php echo form_checkbox('del_bh[]', $item['id']); ?></td>
								<td><?php echo $item['d_start']; ?></td>
								<td><?php echo $item['d_end']; ?></td>
								<td><?php echo $item['t_start']; ?></td>
								<td><?php echo $item['t_end']; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<span class="table_action_buttons">
					<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
				</span>

				<?php echo anchor('admin/ramstrg/create_bh/' . $this->uri->segment(4), lang('ramstrg:add'), 'target="_blank" class="" id="newBH"') ?>


			<?php else: ?>
			<div class="no_data"><?php echo lang('ramstrg:no_items'); ?>
				<?php echo anchor('admin/ramstrg/create_bh/' . $this->uri->segment(4), lang('ramstrg:add'), 'target="_blank" class="green" id="newBH"') ?>
			</div>
		<?php endif;?>

	</fieldset>
	<!-- ende öffnungszeiten -->

</div>		

<div  class="form_inputs" id="sites-panel-holidays">
	<!-- öffnungszeiten --> 
	<fieldset>


		<?php if (!empty($holidays)): ?>

		<table>
			<thead>
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
					<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:from'); ?></th>
					<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:from'); ?></th>
					<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:to'); ?></th>
					<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:to'); ?></th>

				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>

				<?php 
				$day_names = lang('ramstrg:day_names');
				foreach( $holidays as $item ): ?>
				<tr>
					<td><?php echo form_checkbox('del_holidays[]', $item['id']); ?></td>
					<td><?php echo $item['date_start']; ?></td>
					<td><?php echo $item['time_start']; ?></td>
					<td><?php echo $item['date_end']; ?></td>
					<td><?php echo $item['time_end']; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<span class="table_action_buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
	</span>

	<?php echo anchor('admin/ramstrg/create_holidays/' . $this->uri->segment(4), lang('ramstrg:add'), 'target="_blank" class="green" id="newHD"') ?>


<?php else: ?>
	<div class="no_data"><?php echo lang('ramstrg:no_items'); ?>


		<?php echo anchor('admin/ramstrg/create_holidays/' . $this->uri->segment(4), lang('ramstrg:add'), 'target="_blank" class="green" id="newHD"') ?>
	</div>
<?php endif;?>

</fieldset>
<!-- ende öffnungszeiten -->
</div>
</div>
<div class="buttons">
	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save','save_exit', 'cancel') )); ?>
</div>

<?php echo form_close(); ?>

</div> <!-- /content -->

</section>
