<?php
/**
 * Expand a shortened URL
 *
 * @param $url
 * @return mixed
 */

require APPPATH . "third_party/QueryPath/QueryPath.php";

require_once('inc/favicon.class.php');
set_time_limit(0);

/**
 * Tries to find URL from short url services
 *
 * @param $url
 * @return mixed
 */
// TODO: Needs to be improved!
function get_expanded_url($url) {
	$ch = curl_init();
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_HEADER => TRUE,
		CURLOPT_NOBODY => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_FOLLOWLOCATION => TRUE,
		CURLOPT_MAXREDIRS => 10
	);

	curl_setopt_array($ch, $options);
	$data = curl_exec($ch);
	$new_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_close($ch);
	return $new_url;
}

/**
 * set up a flashmessage
 *
 * @return string
 */
function get_flash_message() {
	$obj =& get_instance();
	$flash = '';

	if ($obj->session->flashdata('warning')) $flash = 'warning';
	if ($obj->session->flashdata('error')) $flash = 'error';
	if ($obj->session->flashdata('success')) $flash = 'success';
	if ($obj->session->flashdata('info')) $flash = 'info';

	if ($flash) {
		return '<div class="alert-message ' . $flash . '" data-alert="alert"><a class="close" href="#" rel="close">&times;</a><p>' . $obj->session->flashdata($flash) . '</p></div>';
	}
}

/**
 * Returns favicon source
 *
 * @param $url
 * @return string
 */
function get_favicon($url) {
	return 'http://www.google.com/s2/favicons?domain=' . $url;
}

/**
 * Returns only hostname by url
 *
 * @param $url
 * @return string
 */
function get_hostname($url) {
	$prefix = "/^(https?:\/\/)?([^\/]+)/i";
	preg_match($prefix, $url, $matches);

	$host = $matches[2];

	return $host;
}

/**
 * Tries to fix encoded source
 *
 * @param $str
 * @return string
 */
// TODO: Needs to be fixed, totally screwed! :-/
function convert_to_utf8($str) {

}

/**
 * Returns a button to show tweet on Twitter's homepage
 *
 * @param $item
 * @return string
 */
function create_tweet_link($item) {
	$author = $item['tweet']['user']['screen_name'];
	$id = $item['tweet']['id_str'];
	if (isset($item['tweet']['retweeted_status']['user'])) {
		$author = $item['tweet']['retweeted_status']['user']['screen_name'];
		$id = $item['tweet']['retweeted_status']['id_str'];
	}

	$params = array(
		'class' => 'btn primary',
		'target' => '_blank',
		'rel' => 'close',
	);

	$url = "https://twitter.com/#!/$author/status/$id";

	return anchor($url, 'Show this on Twitter', $params);
}

/**
 * Shows profile image
 *
 * @param $item
 */
function show_profile_image($item) {
	if (isset($item['tweet']['retweeted_status']['user']['profile_image_url'])) :
		$params = array(
			'height' => 40,
			'width' => 40,
			'src' => $item['tweet']['retweeted_status']['user']['profile_image_url'],
			'class' => 'tooltip',
			'data-placement' => 'right',
			'rel' => 'twipsy',
			'title' => 'Tweet from ' . $item['tweet']['retweeted_status']['user']['screen_name']
		);
		echo img($params);

		$params = array(
			'src' => $item['tweet']['user']['profile_image_url'],
			'height' => 20,
			'width' => 20,
			'class' => 'retweet_image tooltip',
			'data-placement' => 'right',
			'rel' => 'twipsy',
			'title' => 'Retweeted by ' . $item['tweet']['user']['screen_name']
		);
		echo img($params);
	else :
		$params = array(
			'height' => 40,
			'width' => 40,
			'src' => $item['tweet']['user']['profile_image_url'],
			'class' => 'tooltip',
			'data-placement' => 'right',
			'rel' => 'twipsy',
			'title' => 'Tweet by ' . $item['tweet']['user']['screen_name']
		);
		echo img($params);
	endif;
}

/**
 * Returns page title of target url, FALSE if not succesful
 *
 * @param $url
 * @return string
 */
function get_page_info($url) {
	$opts = array('ignore_parser_warnings' => TRUE);
	$title = FALSE;
	$encoding = NULL;
	$meta_description = NULL;
	$meta_keywords = array();

	$ch = curl_init();

	$options = array(
		CURLOPT_USERAGENT => 'Mozilla/4.0',
		CURLOPT_URL => $url,
		CURLOPT_HEADER => TRUE,
		CURLOPT_NOBODY => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER => FALSE,
		CURLOPT_SSL_VERIFYHOST => 2
	);
	curl_setopt_array($ch, $options);
	$data = curl_exec($ch);
	$response = curl_getinfo($ch);

	if (stristr($response['content_type'], 'text/html') !== FALSE) {
		switch ($response['http_code']) {
			case 0:
			case 200 :
				$options[CURLOPT_NOBODY] = FALSE;
				curl_setopt_array($ch, $options);
				$contents = curl_exec($ch);
				$title = force_utf8(@qp($contents, 'title', $opts)->text());
				if (!isset($title)) {
					$title = FALSE;
					break;
				}
				$meta_description = force_utf8(@qp($contents, NULL, $opts)->find('meta[name="description"]')->attr('content'));
				$keywords = force_utf8(@qp($contents, NULL, $opts)->find('meta[name="keywords"]')->attr('content'));
				$meta_keywords = explode(',', $keywords);
				$encoding = mb_detect_encoding($contents);
				break;
			default :
				$title = FALSE;
		}
	} else {
		$title = FALSE;
	}
	$title = trim($title);

	array_walk($meta_keywords, 'trim_value');

	if ($title == '')
		$title = FALSE;

	$res = array(
		'transfer_info' => $response,
		'title' => $title,
		'encoding' => $encoding,
		'meta_description' => $meta_description,
		'meta_keywords' => $meta_keywords,
	);

	curl_close($ch);

	return $res;
}

/**
 * Trims text of inner spaces
 *
 * @param $s
 * @return string
 */
function strip_extra_space($s) {
	$newstr = '';
	for ($i = 0; $i < strlen($s); $i++)
	{
		$newstr = $newstr . substr($s, $i, 1);
		if (substr($s, $i, 1) == ' ')
			while (substr($s, $i + 1, 1) == ' ')
				$i++;
	}
	return $newstr;
}

/**
 * Prüft einen String auf UTF-8-Kompatibilität.
 * RegEx von Martin Dürst
 * @source http://www.w3.org/International/questions/qa-forms-utf-8.html
 * @param string $str String to check
 * @return boolean
 */
function is_utf8($str) {
	return preg_match("/^(
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
      )*$/x",
		$str);
}

function force_utf8($str, $inputEnc = 'WINDOWS-1252') {
	if (is_utf8($str)) {
		// Nichts zu tun.
		return $str;
	}

	if (strtoupper($inputEnc) == 'ISO-8859-1') {
		return utf8_encode($str);
	}

	if (function_exists('mb_convert_encoding')) {
		return mb_convert_encoding($str, 'UTF-8', $inputEnc);
	}

	if (function_exists('iconv')) {
		return iconv($inputEnc, 'UTF-8', $str);
	}

	else
	{
		// Alternativ kann man auch den Originalstring ausgeben.
		trigger_error(
			'Kann String nicht nach UTF-8 kodieren in Datei '
					. __FILE__ . ', Zeile ' . __LINE__ . '!', E_USER_ERROR);
	}
}

function show_favicon($host) {
	$params = array(
		'src' => get_favicon($host),
		'title' => $host,
		'height' => 16,
		'width' => 16
	);
	return img($params);
}

function show_hashtags($hashtags, $opentag = '<span class="label success">', $closetag = '</span> ') {
	if (isset($hashtags)) {
		foreach ($hashtags as $hashtag) {
			$text = $hashtag['text'];
			echo $opentag . $text . $closetag;
		}
	}
}

function show_mediacontent($url) {
	$host_name = get_hostname($url);
	$ret = FALSE;

	switch ($host_name) {
		case 'youtube.com':
		case 'www.youtube.com' :
			$vid = youtube_embed($url, '480', '320');
			if (!empty($vid)) $ret = '<div class="video">' . $vid . '</div>';
			break;
		case 'vimeo.com' :
		case 'www.vimeo.com' :
			$vid = vimeo_embed($url, '480', '320');
			if (!empty($vid)) $ret = '<div class="video">' . $vid . '</div>';
			break;
	}
	return $ret;
}

function show_hidefromhost($hostname) {
	return anchor(
		'/hidehost/' . $hostname,
		'&times;',
		array('class' => "tooltip", 'rel' => 'twipsy', 'title' => "Hide links from " . $hostname)
	);
}

function trim_value(&$value) {
	$value = trim($value);
}