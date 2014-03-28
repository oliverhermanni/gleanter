<?php
class Cron extends MY_Controller {

	public function __construct() {
		parent::__construct();

		// not called from cronjob
		if ($this->input->is_cli_request() === FALSE) redirect('/');

		$this->load->model('tweets_model');
	}

	/**
	 * Fill data
	 *
	 * @return int
	 */
	public function fill_db() {
		set_time_limit(0);
		log_message('info','Cronjob started');

		$includes = array('_id','last_tweet_id','tokens');
		$users = $this->user_model->get_all($includes);

		foreach($users as $user) {
			$this->session->sess_destroy();
			// login user
			$tokens = $user['tokens'];
			$this->tweet->set_tokens($tokens);
			$this->tweet->login();
			if (!$this->tweet->logged_in()) continue;

			$params = array(
				'count' => $this->config->item('max_tweets'),
				'since_id' => @$user['last_tweet_id'],
				'include_entities' => TRUE
			);
			if (!isset($user['last_tweet_id']) || ($user['last_tweet_id'] == 0)) {
				unset($params['since_id']);
			}

			$tweets = $this->tweet->call('get','statuses/home_timeline',$params);
			// no new tweets, skip rest, get next user
			if (count($tweets) === 0) continue;

			$data = array();
			foreach($tweets as $item) {
				// no URLs in item, skip
				if (count($item->entities->urls) === 0) continue;

				// tweet already in DB, just add reference
				$tweet_exists = FALSE;
				if ($this->tweets_model->tweet_exists($item->id_str)) {
					$tweet_exists = TRUE;
					$ids[] = $this->add_tweet_reference($item->id_str);
				}
				if ($tweet_exists == TRUE)
					continue;

				if (isset($item->retweeted_status->entities->urls)) {
					$i = count($item->retweeted_status->entities->urls);
					if ($i == 0) {
						$a = reset($item->entities->urls);
					} else {
						$a = reset($item->retweeted_status->entities->urls);
					}
				} else {
					$a = reset($item->entities->urls);
				}
				$expanded_url = get_expanded_url($a->url);
				$page_info = get_page_info($expanded_url);
				$page_title = $page_info['title'];

				if ($page_title == FALSE) {
					$page_title = $item->text;
				}

				$values = array(
					'_id' => $item->id_str,
					'page_title' => force_utf8($page_title),
					'expanded_url' => force_utf8($expanded_url),
					'host_name' => force_utf8(get_hostname($expanded_url)),
					'transfer_info' => $page_info['transfer_info'],
					'encoding' => $page_info['encoding'],
					'meta_description' => force_utf8($page_info['meta_description']),
					'meta_keywords' => $page_info['meta_keywords'],
					'tweet' => $item
				);

				$data[] = $values;

				$ids[$item->id_str] = $this->add_tweet_reference();

			}

			var_dump($ids);
			die();

			$this->tweets_model->save_data($data);
			$this->user_model->save_tweet_ids($user['_id'],$ids);
			$this->user_model->set_last_tweet_id($user['_id'], $tweets[0]->id_str);
		}
		log_message('info','Cronjob finished');
	}


	private function add_tweet_reference() {
		$item = new stdClass();
		$item->created = now();
		return $item;
	}
}
