<h2>User list</h2>
<table>
	<thead>
	<tr>
		<th></th>
		<th>ID</th>
		<th>Screen name (Name)</th>
		<th>Last tweet id</th>
		<th>Tweets total</th>
		<th>Last activity</th>
		<th>User agent</th>
		<th>User group</th>
		<th>Active</th>
		<th>Tools</th>
	</tr>
	</thead>
	<?php foreach ($data as $user): ?>
		<tr>
			<td>
				<?php
					$params = array(
						'src' => $user['profile_image_url'],
						'height' => 16,
						'width' => 16
					);
					echo img($params);
				?>
			</td>
			<td><?= $user['id'] ?></td>
			<td><?= $user['screen_name'].' ('.$user['name'].')'; ?></td>
			<td><?= $user['last_tweet_id'] ?></td>
			<td><?= count($user['tweets']) ?></td>
			<td><?= mysql_datetime($user['last_activity']) ?></td>
			<td><?= $user['user_agent'] ?></td>
			<td><?= $user['user_group'] ?></td>
			<td><?= $user['active'] ?></td>
			<td><a href="/user/reset_user/<?= $user['id'] ?>"><?= image_asset('icons/recycle.png') ?></a></td>
		</tr>
	<?php endforeach; ?>
</table>