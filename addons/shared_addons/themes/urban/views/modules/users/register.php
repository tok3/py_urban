<h2 class="page-title" id="page_title"><?php echo lang('user:register_header') ?></h2>

<div class="row">
	<div class="large-12 columns">
		<p>
			<span><?php echo lang('user:register_step1') ?></span> &rarr;
			<span><?php echo lang('user:register_step2') ?></span>
			<p>
			</div>
		</div>

		<?php if ( ! empty($error_string)):?>
		<!-- Woops... -->
		<div class="error-box">
			<?php echo $error_string;?>
		</div>
	<?php endif;?>

	<?php echo form_open('register', array('id' => 'register')) ?>


	<?php if ( ! Settings::get('auto_username')): ?>
	<div class="row">
		<div class="large-12 columns">
			<label for="username"><?php echo lang('user:username') ?></label>
			<input type="text" name="username" maxlength="100" value="<?php echo $_user->username ?>" id="username"/>
		</div></div>
	<?php endif ?>
	
	<div class="row">
		<div class="large-12 columns">
			<label for="email"><?php echo lang('global:email') ?></label>
			<input type="email" name="email" maxlength="100" value="<?php echo $_user->email ?>" />
			<?php echo form_input('d0ntf1llth1s1n', ' ', 'class="default-form" style="display:none" id="email"') ?>
		</div></div>
		
		<div class="row">
			<div class="large-12 columns">
				<label for="password"><?php echo lang('global:password') ?></label>
				<input type="password" name="password" maxlength="100" id="password"/>
			</div></div>

			<?php foreach($profile_fields as $field) { if($field['required'] and $field['field_slug'] != 'display_name') { ?>
			<div class="row">
				<div class="large-12 columns">
					<label for="<?php echo $field['field_slug'] ?>"><?php echo (lang($field['field_name'])) ? lang($field['field_name']) : $field['field_name'];  ?></label>
					<div class="input"><?php echo $field['input'] ?></div>
				</div></div>
				<?php } } ?>

				
				<div class="row">
					<div class="large-12 columns">
						
						<?php echo form_submit('btnSubmit', lang('user:register_btn')) ?>
					</div></div>

					<?php echo form_close() ?>