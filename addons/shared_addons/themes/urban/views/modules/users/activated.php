<h2 class="page-title" id="page_title"><?php echo lang('user:login_header') ?></h2>

<div class="row">
<div class="large-12 columns">
	<p>
		<div class="success-box">
			<?php echo $this->lang->line('user:activated_message') ?>
		</div>

		<p>
		</div>
	</div>

	<?php echo form_open('users/login', array('id'=>'login')) ?>
	<div class="row">
		<div class="large-12 columns">
			<label for="email"><?php echo lang('global:email') ?></label>
			<?php echo form_input('email','','id="email"')?>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">	<label for="password"><?php echo lang('global:password') ?></label>
			<?php echo form_password('password','','id="password"') ?>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<input type="submit" value="<?php echo lang('user:login_btn') ?>" name="btnLogin" />
		</div>
	</div>
	<?php echo form_close() ?>