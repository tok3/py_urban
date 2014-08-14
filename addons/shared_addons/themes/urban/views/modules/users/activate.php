<h2 class="page-title" id="page_title"><?php echo lang('user:register_header') ?></h2>


<div class="row">
	<div class="large-12 columns">
		<p>
			<a href="{{ url:site }}register"><?php echo lang('user:register_step1') ?></a> &rarr;
			<span><?php echo lang('user:register_step2') ?></span>
			<p>
			</div>
		</div>

		<?php if(!empty($error_string)): ?>
		<div class="error-box">
			<?php echo $error_string ?>
		</div>
	<?php endif;?>
	<?php echo form_open('users/activate', 'id="activate-user"') ?>
	<div class="row">
		<div class="large-12 columns">
			<label for="email"><?php echo lang('global:email') ?></label>
			<?php echo form_input('email', isset($_user['email']) ? $_user['email'] : '', 'maxlength="40" id="email"');?>
		</div></div>

		<div class="row">
			<div class="large-12 columns">
				<label for="activation_code"><?php echo lang('user:activation_code') ?></label>
				<?php echo form_input('activation_code', '', 'maxlength="40" id="activation_code"');?>
			</div></div>

			<div class="row">
				<div class="large-12 columns">

					<?php echo form_submit('btnSubmit', lang('user:activate_btn')) ?>
				</div></div>

				<?php echo form_close() ?>