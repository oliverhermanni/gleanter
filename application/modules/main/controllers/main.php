<?php
class Main extends MY_Controller {

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('twitter/twitter_model');
		$this->load->helper('video');
	}

	public function index() {
		if ($this->tweet->logged_in())
			redirect('/dashboard');

		$this->template->set('invitation_mode',$this->config->item('invitation_mode'));
		$this->build_template('welcome_message');
	}

	//
	public function dashboard() {
		if (!$this->tweet->logged_in()) {
			redirect('/');
		}

		$this->user_model->set_lastactivity();

		$tweet_count = $this->twitter_model->count_records();

		$config['total_rows'] = $tweet_count;
		$config['per_page'] = $this->config->item('items_per_page');
		$config['uri_segment'] = 2;
		$this->pagination->initialize($config);

		$page = ($this->uri->segment(2))?$this->uri->segment(2):0;

		$tweets = $this->twitter_model->get_tweets_by_user($config['per_page'],$page);
		($tweets == FALSE) ? $view = 'empty' : $view = 'index';

		$this->template->title('Dashboard');
		$this->template->set('tweet_count',$tweet_count);
		$this->template->set_partial('tweet','partials/dashboard/tweet');
		$this->template->set_partial('retweet','partials/dashboard/retweet');
		$this->build_template($view,$tweets);
	}

	public function settings() {
		$hidden_hosts = $this->twitter_model->get_hidden_hosts();
		$this->session->set_flashdata('is_settings', TRUE);

		$this->template->title('Settings');
		$this->template->set('hidden_hosts',$hidden_hosts);
		$this->build_template('user/settings');
	}

	public function hide_hostname($hostname) {
		$res = FALSE;
		$res = $this->user_model->hide_hostname($hostname);
		if ($res === TRUE) {
			$msg = "<strong>$hostname</strong> will no longer be displayed in your gleanter timeline. You can undo now or change this later in your personal settings";
			$msg .= '<div class="alert-actions">';
			$msg .=  anchor('/showhost/'.$hostname,'Undo',array('class' => 'btn small')).' ';
			$msg .=  anchor('#','Close',array('class' => 'btn small', 'rel' => 'close'));
			$msg .= '</div>';
			$this->session->set_flashdata('info',$msg);
			redirect('/dashboard');
		}
	}

	public function show_hostname($hostname) {
		$res = FALSE;
		$res = $this->user_model->show_hostname($hostname);
		$this->session->flashdata('is_settings') === TRUE ? $redirect = '/settings' : $redirect = '/dashboard';
		redirect($redirect);
	}

}