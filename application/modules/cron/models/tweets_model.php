<?php
class Tweets_model extends MY_Model {

	var $primary_table = 'tweets';

	public function save_data($data) {
		if (count($data) > 0)
			$this->mongo_db->batch_insert('tweets',$data);
	}

	public function tweet_exists($id) {
		$options = array(
			'_id' => $id
		);

		$res = $this->mongo_db->select(array())
				->where($options)
				->get($this->primary_table);

		return (count($res) !== 0);
	}

}