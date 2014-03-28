<?php
function mysql_datetime($date=null) {
    if(!$date) {
        // use now() instead of time() to adhere to user setting
        $date = now();
        }
    if(is_numeric($date) && strlen($date)==10) {
        return mdate("%Y-%m-%d %H:%i:%s", $date);
        }   else    {
        // try to use now()
        return mdate("%Y-%m-%d %H:%i:%s", now());
        }
    }

/**
 * Format
 *
 * @param $time
 * @return string
 */
function format_twitter_datetime($time) {
	$time = strtotime($time);
	$format = '%d.%m.%Y - %H:%M';
	return strftime($format, $time);
}
