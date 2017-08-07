<?php
/**
 * Type, min length and emptiness validation
 * @param string $val
 * @param string $type
 * @param int $min_len
 * @return bool
 */
function strNumValid($val = '',$type = 's', $min_len = 0) {
    if(!empty($val)) {
		if(strlen($val)>=$min_len) {
			if($type == 'n' && ctype_digit($val)) {
				return true;
			}
			else if($type == 's' && is_string($val) && !ctype_digit($val)) {
				return true;
			}
			else return false;
		}
	}
	return false;
}

/**
 * Password validation
 * @param $pwd
 * @return bool
 */
function passValid($pwd) {
    if(!empty($pwd)) {
		if(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $pwd)) {
			return true;
		}
	}
	return false;
}

/**
 * Email validation
 * @param $email
 * @return bool
 */
function emailValid($email) {
	if(!empty($email)) {
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
	}
	return false;	
}

/**
 * Url validation
 * @param $url
 * @return bool
 */
function urlValid($url) {
	if (!empty($url)) {
		if(filter_var($url, FILTER_VALIDATE_URL)) return true;
	}
	return false;
}
