<?php

/**
 * Twim Twitter Helper
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
 * @since     File available since Release 1.0
 */
App::uses('AppHelper', 'View/Helper');
App::uses('TwimAutolink', 'Twim.Lib');
if (!class_exists('Twitter_Extractor')) {
	require_once dirname(dirname(dirname(__FILE__))) . '/Vendor/twitter-text-php/lib/Twitter/Extractor.php';
}

/**
 * @property HtmlHelper $Html
 * @property FormHelper $Form
 * @property JsHelper $Js
 */
class TwitterHelper extends AppHelper {

	public $helpers = array('Html', 'Form', 'Js');

	/**
	 * create tweet box
	 *
	 * @param $fieldName
	 * @param $options
	 *      type: element type (default: textarea)
	 *      maxLength:   text max length (default: 140)
	 *      counterText: length message
	 *      submit: submit button message. if set to false, not create.
	 *      jqueryCharCount: path to charCount.js (jquery plugin)
	 *      other keys set to input element options.
	 */
	public function tweet($fieldName, $options = array()) {
		$this->setEntity($fieldName);
		$domId = !empty($options['id']) ? $options['id'] : $this->domId($fieldName);

		$default = array(
			'type' => 'textarea',
			'maxlength' => 140,
			'jqueryCharCount' => '/twim/js/charCount.js',
			'counterText' => __d('twim', 'Characters left: '),
			'submit' => __d('twim', 'Tweet'),
		);

		$options = am($default, $options);
		$inputOptions = $options;
		unset($inputOptions['jqueryCharCount']);
		unset($inputOptions['counterText']);
		unset($inputOptions['submit']);

		$out = $this->Html->script($options['jqueryCharCount']);

		$out .= $this->Form->input($fieldName, $inputOptions);

		$out .= $this->Js->buffer("
            $('#{$domId}').charCount({
                limit: {$options['maxlength']},
                counterText: '{$options['counterText']}',
                exceeded: function(element) {
                    $('#{$domId}Submit').attr('disabled', true);
                },
                allowed: function(element) {
                    $('#{$domId}Submit').removeAttr('disabled');
                }
            });
        ");

		if ($options['submit']) {
			$out .= $this->Form->submit($options['submit'], array('id' => $domId . 'Submit'));
		}

		return $this->output($out);
	}

	/**
	 * create OAuth Link
	 *
	 * @param $options
	 *  login:        login link text
	 *  datasource:   datasource name (default: twitter)
	 *  authorize: use authorize link (default: false)
	 */
	public function oauthLink($options = array()) {
		$default = array(
			'login' => __d('twim', 'Login Twitter'),
		);

		if (is_string($options)) {
			$options = array('login' => $options);
		}
		$options = am($default, $options);

		$login = $options['login'];
		unset($login);

		// create connect url
		$url = array('plugin' => 'twim', 'controller' => 'oauth', 'action' => 'connect');
		if (isset($options['datasource'])) {
			$url['datasource'] = $options['datasource'];
			unset($options['datasource']);
		}
		if (isset($options['authorize'])) {
			$url['authorize'] = $options['authorize'];
			unset($options['authorize']);
		}

		if (Configure::read('Routing.prefixes')) {
			foreach (Configure::read('Routing.prefixes') as $prefix) {
				$url[$prefix] = false;
			}
		}

		return $this->Html->link($options['login'], $url, $options);
	}

	/**
	 * linkify text
	 *
	 * @param string $value
	 * @param array  $options
	 *    username: linkify username. eg. @username
	 *    hashtag : linkify hashtag. eg. #hashtag
	 *    url     : linkify url. eg. http://example.com/
	 * @return string
	 */
	public function linkify($value, $options = array()) {
		$default = array(
			'url' => true,
			'username' => true,
			'hashtag' => true,
		);
		$options = am($default, $options);

		$extractor = Twitter_Extractor::create($value);
		$linker = TwimAutolink::create($value, $options);
		$entities = array();

		// autolink
		if ($options['url']) {
			$entities = am($entities, $extractor->extractURLWithoutProtocol(false)->extractURLsWithIndices());
		}
		if ($options['hashtag']) {
			$entities = am($entities, $extractor->extractHashtagsWithIndices());
		}
		if ($options['username']) {
			$entities = am($entities, $extractor->extractMentionsOrListsWithIndices());
		}
		$entities = $extractor->removeOverlappingEntities($entities);

		$tweet = $linker->autoLinkEntities($value, $entities);
		return $tweet;
	}

	/**
	 * create tweet button
	 *
	 * @link http://dev.twitter.com/pages/tweet_button
	 * @param string  $label
	 * @param array   $options
	 * @param boolean $dataAttribute
	 * @param boolean $scriptInline
	 * @return string
	 */
	public function tweetButton($label = null, $options = array(), $dataAttribute = false, $scriptInline = false) {
		$attributes = array();

		$defaults = array(
			'class' => 'twitter-share-button',
			'url' => '',
			'via' => '',
			'text' => '',
			'related' => '',
			'count' => 'horizontal', // 'none', 'vertical'
			'lang' => 'en',
			'counturl' => '',
		);

		if (empty($label)) {
			$label = 'Tweet';
		}

		$options = am($defaults, $options);

		$attributes['class'] = $options['class'];
		unset($options['class']);

		$options['count'] = strtolower($options['count']);
		if (!in_array($options['count'], array('none', 'horizontal', 'vertical'))) {
			$options['count'] = 'none';
		}

		$options = Set::filter($options);

		if ($dataAttribute) {
			foreach ($options as $key => $val) {
				$attributes['data-' . $key] = $val;
			}
			$options = array();
		}

		$out = $this->Html->link($label, 'http://twitter.com/share' . Router::queryString($options), $attributes);
		$out .= $this->Html->script('http://platform.twitter.com/widgets.js', array('inline' => $scriptInline));
		return $this->output($out);
	}

	/**
	 *
	 * @param string $path
	 * @param array  $options
	 * @return @return string completed img tag
	 */
	public function image($path, $options = array()) {
		if (preg_match('!^http://a[0-9]+\.twimg\.com/!', $path) && env('HTTPS')) {
			$path = preg_replace('!^http://a[0-9]+\.twimg\.com/!', 'https://s3.amazonaws.com/twitter_production/', $path);
		}
		return $this->Html->image($path, $options);
	}

}
