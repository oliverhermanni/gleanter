<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="description" content="gleanter helps you with links in your personal Twitter timeline."/>
	<meta name="keywords" content="twitter,links,collect,timeline, find, tweets"/>
	<title>gleanter | <?php echo $template['title'] ?></title>
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<!-- Le styles -->
	<?php
	$css_files = array(
		'bootstrap.min.css',
		'style.css'
	);
	$css_files = array_merge($more_css, $css_files);

	foreach ($css_files as $css) {
		echo css_asset($css) . "\n";
	}

	$js_files = array(
		'bootstrap/bootstrap-modal.js',
		'bootstrap/bootstrap-dropdown.js',
		'bootstrap/bootstrap-twipsy.js',
		'bootstrap/bootstrap-popover.js',
		'bootstrap/bootstrap-tabs.js',
		'default.js'
	);
	$js_files = array_merge($more_js, $js_files);

	foreach ($js_files as $js) {
		echo js_asset($js) . "\n";
	}

	?>

	<!-- Le fav and touch icons -->
	<link rel="shortcut icon" href="images/favicon.ico">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-26451681-1']);
		_gaq.push(['_setDomainName', '.gleanter.com']);
		_gaq.push(['_trackPageview']);

		(function () {
			var ga = document.createElement('script');
			ga.type = 'text/javascript';
			ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();
	</script>
</head>

<body id="bootstrap-js">

<div class="topbar">
	<div class="fill">
		<div class="container-fluid">
			<a class="brand" href="/"><?= image_asset('logo.png'); ?></a>
			<?php echo $template['partials']['navigation'] ?>
			<?php echo $template['partials']['admin'] ?>
			<?php echo $template['partials']['user'] ?>
		</div>
	</div>
</div>

<div class="container-fluid">

	<div class="container">
		<?= get_flash_message(); ?>
	</div>
	<?= $template['body'] ?>


	<footer>
		<div class="container footermenu">
			<div class="row">
				<div class="span8">
					<ul>
						<li><?= anchor('/p/about','About') ?></li>
						<li><?= anchor('/p/imprint','Imprint')?></li>
					</ul>
				</div>
				<div class="span3 offset5" style="text-align: right;">
					<a href="https://twitter.com/#!/gleanter" class="twitter_link" target="_blank">Follow us on Twitter</a>
				</div>
			</div>
		</div>
		<p>&copy; gleanter 2011</p>

		<p>Page rendered in <strong>{elapsed_time}</strong> seconds</p>
		<?php
		if (defined('ENVIRONMENT')) {
			switch (ENVIRONMENT) {
				case 'development':
					echo '<pre>'.mysql_datetime($this->session->userdata('last_activity')).'</pre>';
					echo '<pre>';
					var_dump($this->session->all_userdata());
					echo '</pre>';
					break;
			}
		}

		?>
	</footer>

</div>
<!-- /container -->
</body>
</html>
