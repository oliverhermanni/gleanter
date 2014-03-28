<?php
class Pages extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function index($view) {

		if (!file_exists(APPPATH."views/pages/$view.php")) {
			show_404();
		}

		$this->build_template("pages/$view");
	}
}
