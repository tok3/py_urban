<?php if ($comments): ?>
	<?php foreach ($comments as $item): ?>

	<div class="row comment">
		<div class="columns medium-2 small-2">
<span class="right thumbnail">
			<?php echo gravatar($item->user_email, 60) ?>
		</span>
		</div>
		<div class="columns medium-10 small-10 ">
			<h5><?php echo $item->user_name ?></h5>
			<h6><?php echo format_date($item->created_on) ?></h6>
				<?php if (Settings::get('comment_markdown') and $item->parsed): ?>
				<?php echo $item->parsed ?>
			<?php else: ?>
			<p><?php echo nl2br($item->comment) ?></p>
		<?php endif ?>

</div>

</div><!-- close .comment -->
<?php endforeach ?>

<?php else: ?>
	<p><?php echo lang('comments:no_comments') ?></p>
<?php endif ?>