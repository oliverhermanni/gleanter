<div class="content">
	<ul class="pills">
		<li class="active"><a href="#hiddencontent">Hidden content</a></li>
	</ul>

	<div class="pill-content">
		<div class="active" id="hiddencontent">
			<h4>Hidden hostnames</h4>
			<p>
				Here you can re-enable hostnames you previously removed to display.
			</p>
			<table class="bordered-table">
				<tr>
					<th>Hostname</th>
					<th>Action</th>
				</tr>
				<?php foreach ($hidden_hosts as $host): ?>
					<tr>
						<td>
							<span class="host" style="background: url(<?=get_favicon($host) ?>) no-repeat;">
							<?= $host ?>
							</span>
						</td>
						<td><?=anchor('/showhost/'.$host,"enable") ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>

</div>