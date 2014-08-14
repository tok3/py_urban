


<h2 class="page-title" id="page_title"><?php echo lang('user:login_header') ?></h2>

<?php if (validation_errors()): ?>
	<div class="error-box">
		<?php echo validation_errors();?>
	</div>
<?php endif ?>

<?php echo form_open('users/login', array('id'=>'login'), array('redirect_to' => $redirect_to)) ?>
<div class="row">
	<div class="large-12 columns">
		<label for="email"><?php echo lang('global:email') ?>
		</label>
		<?php echo form_input('email', $this->input->post('email') ? $this->input->post('email') : '','id="email"')?>

	</div>
</div>
	 <div class="row">
	<div class="large-12 columns">
		<label for="password"><?php echo lang('global:password') ?></label>
		<input type="password" id="password" name="password" maxlength="20" />
	</div>
</div>
	<div class="row">
	<div class="large-12 columns">
		<?php echo form_checkbox('remember', '1', false,'id="remember"') ?>
		<label  for="remember"><?php echo lang('user:remember') ?></label>
	</div>
</div>
<div class="row">
	<div class="large-12 columns">
		<input type="submit" value="<?php echo lang('user:login_btn') ?>" name="btnLogin" /> <span class="register"> | <?php echo anchor('register', lang('user:register_btn'));?>
	</span>
	</div>
</div>
<div class="row">
	<div class="large-12 columns">
			<?php echo anchor('users/reset_pass', lang('user:reset_password_link'));?>
			</div>
</div>
<?php echo form_close() ?>
