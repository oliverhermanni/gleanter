<?php
class User extends MY_Controller {

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('twitter/twitter_model');
		$this->load->model('invitation_model');
	}

	/**
	 * User login
	 */
	public function login() {
		// User tried to login first time, but has no invite code
		if ($this->session->userdata('has_invite') === 'no') {
			$this->session->set_flashdata('error','Invite code not found or already used!');
			$this->session->unset_userdata('invite_code');
			$this->session->unset_userdata('has_invite');
			$this->tweet->logout();
			redirect('/');
		}

		$redirect = FALSE;

		if (!$this->tweet->logged_in()) {
			$id = $this->session->userdata('_id');
			// User has existing session
			if (($id !== FALSE) && ($id !== NULL)) {
				$redirect = $this->_loginuser($id);
			}
			$this->tweet->set_callback(site_url().'/login');
			$this->tweet->login();
			if ($redirect === TRUE) {
				redirect('/main');
			}
		} else {
			// load user from db or create a new one
			$credentials = $this->tweet->call('get', 'account/verify_credentials');
			$options = array(
				'_id' => $credentials->id_str
			);
			$user = $this->user_model->get($options);


			if ($user === FALSE) {
				if (($this->config->item('invitation_mode') === TRUE) && ($this->session->userdata('invite_code') === FALSE)) {
					$this->session->set_flashdata('error','You are not registered! Did you get an invite code?');
					$this->tweet->logout();
					redirect('/');
				}
				$options = array(
					'_id' => $credentials->id_str,
					'screen_name' => $credentials->screen_name,
					'name' => $credentials->name,
					'profile_image_url' => $credentials->profile_image_url,
					'tokens' => $this->tweet->get_tokens(),
					'active' => TRUE,
					'created' => $this->mongo_db->date(),
					'tweets' => array(),
					'last_tweet_id' => '0',
					'user_group' => GLEANTER_USER
				);
				$res = $this->user_model->create($options);
				$options = array(
					'_id' => $credentials->id_str
				);
				$user = $this->user_model->get($options);
			}
			$this->invitation_model->disable_code($this->session->userdata('invite_code'));
			$this->set_user_session($user[0]);
			redirect('/main');
		}
	}

	/**
	 * @param $id
	 * @return bool
	 */
	private function _loginuser($id) {
		$options = array(
			'_id' => $id
		);
		$user = $this->user_model->get($options);

		if ($user === FALSE) {
			$this->session->set_flashdata('error','USER: Error while trying to login existing user!');
			redirect('/');
		}
		$this->set_user_session($user[0]);
		$tokens = $user[0]['tokens'];
		$this->tweet->set_tokens($tokens);
		return TRUE;
	}


	/**
	 * Logout user
	 */
	public function logout() {
		$this->tweet->logout();
		redirect('/');
	}

	/**
	 * Set user data in session
	 *
	 * @param $user
	 */
	public function set_user_session($user) {
		$this->session->unset_userdata('has_invite');
		$this->session->unset_userdata('invite_code');
		$this->session->set_userdata(
			array(
				'_id' => $user['_id'],
				'screen_name' => $user['screen_name'],
				'profile_img_url' => $user['profile_image_url']
			)
		);
	}

	/**
	 * Invitation system
	 */
	public function invite() {
		$code = $this->input->post('code');
		$options = array(
			'code' => $code,
			'active' => 1
		);
		$invite = $this->invitation_model->get($options);
		$res = $invite->result();

		if (!empty($res)) {
			$this->session->set_userdata('has_invite','yes');
			$this->session->set_userdata('invite_code',$code);
		} else {
			$this->session->set_userdata('has_invite','no');
		}

		redirect('/login');
	}

	/**
	 * User list for admin mode
	 */
	public function list_users() {
		$this->redirect_if_not_admin();

		$users = $this->user_model->get_all();
		$this->build_template('list_users',$users);
	}

	/**
	 * Remove users' tweet references, set last_tweet_id = 0
	 */
	public function reset_user() {
		$this->redirect_if_not_admin();
		$user = $this->uri->segment(3);

		$this->user_model->set_last_tweet_id($user,0);
		$this->user_model->clear_tweet_ids($user);
		redirect('/user/list_users');
	}

}