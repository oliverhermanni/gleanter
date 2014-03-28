<?php
class Twitter_model extends CI_Model {
	var $primary_table = 'tweets';

	/**
	 * Count tweets by user
	 *
	 * @param string $id
	 * @return int
	 */
	public function count_records($id = '') {
		if ($id === '')
			$id = $this->session->userdata('id');

		if ($id === FALSE) return FALSE;
		$tweets = $this->get_tweets_by_user();

		return (count($tweets));
	}

	public function get_hidden_hosts($id = '') {
		$hidden_hosts = array();

		if ($id === '')
			$id = $this->session->userdata('id');

		if ($id === FALSE) return FALSE;

		$hidden_hosts = $this->mongo_db->select(array('hidden_hosts'))
						->where(array('_id' => $id))
						->get('users');

		isset($hidden_hosts[0]['hidden_hosts']) ? $hidden_hosts = $hidden_hosts[0]['hidden_hosts'] : $hidden_hosts = array();

		return $hidden_hosts;
	}

	public function get_tweets_by_user($limit=0,$offset=0,$id = '') {
		$tweets = array();
		$ids = array();

		if ($id === '')
			$id = $this->session->userdata('id');

		if ($id === FALSE) return FALSE;

		$tweet_ids = $this->mongo_db->select(array('tweets'))
						->where(array('_id' => $id))
						->get('users');

		$tweet_ids = $tweet_ids[0]['tweets'];

		foreach($tweet_ids as $item) {
			$ids[] = $item['id_str'];
		}

		if (count($ids) === 0) {
			return FALSE;
		}

		$tweets = $this->mongo_db
					->where_not_in('host_name',$this->get_hidden_hosts())
					->where_in('id',$ids)
					->limit($limit)
					->offset($offset)
					->order_by(array('tweet.id_str' => 'DESC'))
					->get('tweets');

		if(count($tweets) == 0)
			return FALSE;

		return $tweets;
	}
}