<div class="container">
	<div class="hero-unit">
		<h2>Welcome to gleanter!</h2>

		<h3>Miss important links on your Twitter timeline? We'll help you with that!</h3>

		<p>
			With gleanter you can:
		</p>
		<ul>
			<li>Collect your links on Twitter</li>
			<li>Arrange them in lists</li>
			<li>Share cool links with your friends</li>
			<li>Export them to other services (eg. Delicious)</li>
		</ul>

		<?php if ($invitation_mode) : ?>

		<p>
			Currently we are in 'invitation only' mode. Do you have a code?
		</p>
		<?php
		echo form_open('/invite');
		$data = array(
			'name' => 'code',
			'id' => 'code',
			'class' => 'span3'
		);
		echo form_input($data);
		$data = array(
			'type' => 'submit',
			'class' => 'primary btn',
			'content' => 'Submit code'
		);
		echo form_button($data);
		echo form_close();
		?>
		<h5>
			If you have already registered, you may continue <?= anchor('/login', 'here') ?>.
		</h5>

		<?php else: ?>

		<a href="/login" class="btn large primary">Login or register - it's FREE!</a>

		<?php endif; ?>
	</div>
</div>