
<!-- blog view -->

{{ post }}

<article>

	<h3><a href="{{ base:url }}blog{{ url }}">{{ theme:image file="link.png" }} {{ title }}</a></h3>
	<h6>{{ helper:lang line="blog:written_by_label" }} <strong>{{ created_by:display_name }}
	</strong>, {{ helper:date timestamp=created_on format="F j, Y" }}</h6>

	<p class="preview postHead">
		{{ preview }}
	</p>

	<p class="post_body">
		{{ body }}
	</p>
	<h5><a href="{{ base:url }}blog#{{slug}}" title="Back to the blog">&larr; Back</a></h5>

	<div class="post_meta">
		{{ if keywords }}
		<div class="keywords">
			{{ keywords }}
			<span class="label secondary radius"><a href="{{ url:site }}blog/tagged/{{ keyword }}">{{ keyword }}</a></span>
			{{ /keywords }}
		</div>
		{{ endif }}
	</div>
</article>
{{ /post }}

<?php if (Settings::get('enable_comments')): ?>

	<div id="comments">
		<br>

		<div id="existing-comments">
			<h4><?php echo lang('comments:title') ?></h4>
			<?php echo $this->comments->display() ?>
		</div>

		<?php if ($form_display): ?>

		{{ if session:messages != ''}}

			<script type="text/javascript">//<![CDATA[
			window.location.href = "{{ url:current }}#commentsMsg";
			//]]></script>

			<span id="commentsMsg">
				{{ session:messages success="commentsMsg label success radius medium-5 small-12" notice="commentsMsg label notice radius medium-5 small-12" error="commentsMsg label alert warning radius medium-5 small-12" }}
			</span>
			{{ endif }}

			<?php echo $this->comments->form() ?>

		</div>
	<?php else: ?>
	<?php echo sprintf(lang('blog:disabled_after'), strtolower(lang('global:duration:'.str_replace(' ', '-', $post[0]['comments_enabled'])))) ?>
<?php endif ?>

<?php endif ?>


