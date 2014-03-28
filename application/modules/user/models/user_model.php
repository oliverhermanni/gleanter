<?php
class User_model extends CI_Model{
	public $primary_table = 'users';

	public function get($options = array()) {
		$user = $this->mongo_db->select()
			->where($options)
			->limit(1)
			->get($this->primary_table);

		if (empty($user)) {
			return FALSE;
		}

		return $user;
	}

	public function create($options = array()) {
		if (empty($options))
			return FALSE;

		return $this->mongo_db->insert('users',$options);
	}

	public function get_user_group() {
		if (!$this->tweet->logged_in())
			return FALSE;

		$options = array(
			'_id' => $this->session->userdata('_id')
		);

		$group = $this->mongo_db->select(array('user_group'))
				->where($options)
				->get($this->primary_table);

		return $group[0]['user_group'];
	}

	public function get_all($includes = array(), $excludes = array()) {
		$users = $this->mongo_db
				->select($includes, $excludes)
				->where(array('active' => TRUE))
				->get($this->primary_table);
		return $users;

	}

	public function save_tweet_ids($userid,$ids){
		$this->mongo_db->where(array('_id' => $userid))
			->push('tweets',$ids)
			->update($this->primary_table);

	}

	public function clear_tweet_ids($userid) {
		$this->mongo_db->where(array('_id' => $userid))
			->set('tweets',array())
			->update($this->primary_table);
	}

	public function set_last_tweet_id($userid,$last_tweet_id) {
		$this->mongo_db->where(array('_id' => $userid))
			->set('last_tweet_id', $last_tweet_id)
			->update($this->primary_table);
	}

	public function hide_hostname($hostname) {
		$userid = $this->session->userdata('_id');

		$ret = $this->mongo_db->where(array('_id' => $userid))
			->addtoset('hidden_hosts',$hostname)
			->update($this->primary_table);
		return $ret;
	}

	public function show_hostname($hostname) {
		$userid = $this->session->userdata('_id');

		$ret = $this->mongo_db->where(array('_id' => $userid))
			->pull('hidden_hosts',$hostname)
			->update($this->primary_table);
		return $ret;

	}

	public function set_lastactivity() {
		$userid = $this->session->userdata('_id');

		if ($userid === FALSE)
			return FALSE;

		$this->mongo_db->where(array('_id' => $userid))
			->set('user_agent', $this->session->userdata('user_agent'))
			->set('ip_address', $this->session->userdata('ip_address'))
			->set('last_activity', $this->session->userdata('last_activity'))
			->update($this->primary_table);
	}

	public function set_userdata() {
		$userid = $this->session->userdata('_id');

		if ($userid === FALSE)
			return FALSE;

		$this->mongo_db->where(array('_id' => $userid))
			->set('user_agent', $this->session->userdata('user_agent'))
			->set('ip_address', $this->session->userdata('ip_address'))
			->set('last_activity', $this->session->userdata('last_activity'))
			->update($this->primary_table);
	}
}