<div class="container-fluid">
	<div class="sidebar">
		<div class="widget">
			<div class="row">
			<span style="float: left; margin-right: 5px;">
				<?= img(array('height' => 48, 'width' => 48, 'src' => $this->session->userdata('profile_img_url'))); ?>
			</span>
				<h4><?=$this->session->userdata('screen_name')?></h4>
			</div>
			<div class="row">
				<ul class="userinfo">
					<li><h2><?= $tweet_count ?></h2> links total</li>
					<li><h2>0</h2>lists</li>
					<li><h2>0</h2>friends</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="content">
		<table class="items">
			<?php foreach ($data as $item) : ?>
				<tr>
					<td class="profile_image">
						<?= show_profile_image($item) ?>
					</td>
					<td>
						<h5 class="tweetheader" id="item_<?=$item['id']?>">
							<?=	character_limiter($item['page_title'], 140); ?>
						</h5>
						<span class="host" style="background: url(<?=get_favicon($item['host_name']) ?>) no-repeat;">
							<?= $item['host_name'] ?>
							<?= show_hidefromhost($item['host_name']) ?>
						</span>
						<span class="created_at" title="<?= format_twitter_datetime($item['tweet']['created_at']) ?>">
							<?= timespan(strtotime($item['tweet']['created_at'], time())) ?> ago
						</span>
						<div class="iteminfo" rel="item_<?=$item['id']?>">
							<?php if (!empty($item['meta_description'])) : ?>
							<div class="metadescription">
								<strong>Description</strong><br />
								<?= $item['meta_description'] ?>
							</div>
							<?php endif; ?>
							<?= show_mediacontent($item['expanded_url']) ?>
							<div class="url">
								<strong>Expanded URL</strong><br/>
								<?=auto_link($item['expanded_url'],"both",true)?>
							</div>
							<div class="tweet">
								<strong>Original tweet</strong>
								<div class="tweet_text"><?= auto_link($item['tweet']['text'],'both',TRUE) ?></div>
								<div class="hashtags"><?= show_hashtags($item['tweet']['entities']['hashtags']); ?></div>
							</div>
						</div>
					</td>
					<td>
						<?= anchor(
								$item['expanded_url'],
								image_asset('icons/link_go.png'),
								array('target' => '_blank', 'class' => "tooltip", 'rel' => 'twipsy', 'title' => 'Visit the link')
							); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php echo $this->pagination->create_links() ?>
	</div>
</div>