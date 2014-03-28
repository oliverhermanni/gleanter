<?php
class MY_Controller extends MX_Controller {
	public $favicon_tool_url;

	function __construct() {
		parent::__construct();
		$this->config->load('gleanter');
	}

	/**
	 * Setup default settings for template
	 *
	 * @param $view
	 * @param null $data
	 * @param null $additional_files
	 * @return mixed
	 */
	function build_template($view, $data = NULL, $additional_files = NULL) {
		$this->tweet->logged_in() ? $login_partial = 'partials/user/logged_in' : $login_partial = 'partials/user/login';
		$this->tweet->logged_in() ? $navigation_partial = 'partials/navigation/logged_in' : $navigation_partial = 'partials/navigation/login';
		$this->user_model->get_user_group() == GLEANTER_ADMIN ? $admin_partial = 'partials/admin/admin' : $admin_partial = 'partials/admin/empty';

		// don't hate me for that, it's just for beautifying!
		if (!is_array(@$additional_files['css']))
			$additional_files['css'] = array();
		if (!is_array(@$additional_files['js']))
			$additional_files['js'] = array();

		$this->template->set('more_css',$additional_files['css']);
		$this->template->set('more_js',$additional_files['js']);

		return $this->template
				->set('data',$data)
				->set_partial('user',$login_partial)
				->set_partial('admin',$admin_partial)
				->set_partial('navigation',$navigation_partial)
				->build($view);
	}

	public function redirect_if_not_admin($target = '/') {
		if (($this->user_model->get_user_group() != GLEANTER_ADMIN) && (!$this->tweet->logged_in()))
			redirect($target);
	}
}