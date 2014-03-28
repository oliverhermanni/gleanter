<ul class="nav secondary-nav pull-right">
	<li class="dropdown" data-dropdown="dropdown">
		<a href="#" class="dropdown-toggle">
			Welcome back, <?php echo $this->session->userdata('screen_name') ?>!</a>
		<ul class="dropdown-menu">
			<li><a href="/main/settings">Settings</a></li>
			<li class="divider"></li>
			<li><a href="/user/logout">Logout</a></li>
		</ul>
	</li>
</ul>

