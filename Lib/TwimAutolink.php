<?php

/**
 * Twitter_Autolink wrapper
 *
 * CakePHP 2.x
 * PHP version 5
 *
 * Copyright 2013, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   2.1
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2013 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   Twim
 * @since     File available since Release 2.0
 */
if (!class_exists('Twitter_Autolink')) {
	require_once dirname(dirname(__FILE__)) . '/Vendor/twitter-text-php/lib/Twitter/Autolink.php';
}

class TwimAutolink extends Twitter_Autolink {

/**
 * Provides fluent method chaining.
 *
 * @param  string  $tweet        The tweet to be converted.
 * @param  array   $options      Initialize options
 *
 * @see  __construct()
 *
 * @return  TwimAutolink
 */
	public static function create($tweet = null, $options = array()) {
		$defaults = array(
			'escape' => false,
			'full_encode' => false,
			'class_url' => 'url',
			'class_user' => 'username',
			'class_list' => 'list',
			'class_hash' => 'hashtag',
			'url_base_user' => 'https://twitter.com/',
			'url_base_list' => 'https://twitter.com/',
			'url_base_hash' => 'https://twitter.com/#!/search?q=%23',
			'nofollow' => true,
			'external' => true,
			'target' => '_blank',
		);
		$options = am($defaults, $options);

		$autoLink = new self($tweet, $options['escape'], $options['full_encode']);
		$autoLink
			->setURLClass($options['class_url'])
			->setUsernameClass($options['class_user'])
			->setListClass($options['class_list'])
			->setHashtagClass($options['class_hash'])
			->setNoFollow($options['nofollow'])
			->setExternal($options['external'])
			->setTarget($options['target'])
			->setUrlBaseUser($options['url_base_user'])
			->setUrlBaseList($options['url_base_list'])
			->setUrlBaseHash($options['url_base_hash'])
		;
		return $autoLink;
	}

/**
 * @param string $tweet
 * @return \TwimAutolink
 */
	public function setTweet($tweet) {
		$this->tweet = $tweet;
		return $this;
	}

/**
 * @return string
 */
	public function getTweet() {
		return $this->tweet;
	}

/**
 * @param string $v
 * @return \TwimAutolink
 */
	public function setUrlBaseUser($v) {
		$this->url_base_user = trim($v);
		return $this;
	}

/**
 * @param string $v
 * @return \TwimAutolink
 */
	public function setUrlBaseList($v) {
		$this->url_base_list = trim($v);
		return $this;
	}

/**
 * @param string $v
 * @return \TwimAutolink
 */
	public function setUrlBaseHash($v) {
		$this->url_base_hash = trim($v);
		return $this;
	}

}
