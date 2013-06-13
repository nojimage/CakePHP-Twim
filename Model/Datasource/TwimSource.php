<?php

/**
 * Twitter API Datasource
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
 *
 */
App::uses('RestSource', 'Rest.Model/Datasource');
App::uses('HttpSocketOauth', 'Twim.Network/Http');

/**
 *
 */
class TwimSource extends RestSource {

/**
 *
 * @var array
 */
	public $_baseConfig = array(
		'oauth_consumer_key' => '',
		'oauth_consumer_secret' => '',
		'oauth_token' => '',
		'oauth_token_secret' => '',
		'oauth_callback' => '',
		'throw_exception' => true,
		'cache' => false,
		'refresh_cache' => false,
		'proxy' => array(
			'host' => '',
			'port' => 3128,
			'method' => null,
			'user' => null,
			'pass' => null,
		),
	);

/**
 * Overrides RestSource constructor to use the OAuth extension to CakePHP's
 * default HttpSocket class to issue the requests.
 *
 * If no config is passed into the constructor, i.e. the config is not in
 * app/config/database.php check if any config is in the config directory of
 * the plugin, or in the configure class and use that instead.
 *
 * @param array $config
 */
	public function __construct($config = null) {
		if (!is_array($config)) {
			$config = array();
		}

		// Default config
		$defaults = array(
			'datasource' => 'Twim.TwimSource',
		);

		// Try and import the plugins/twim/config/twitter_config.php file and
		// merge the config with the defaults above
		if (App::import(array('type' => 'File', 'name' => 'Twim.TWITTER_CONFIG', 'file' => 'config' . DS . 'twitter_config.php'))) {
			$TWITTER_CONFIG = new TWITTER_CONFIG();
			if (isset($TWITTER_CONFIG->twitter)) {
				$defaults = array_merge($defaults, $TWITTER_CONFIG->twitter);
			}
		}

		// Add any config from Configure class that you might have added at any
		// point before the model is instantiated.
		if (($configureConfig = Configure::read('Twitter.config')) != false) {
			$defaults = array_merge($defaults, $configureConfig);
		}

		$config = array_merge($defaults, $config);
		parent::__construct($config, new HttpSocketOauth());

		$this->Http->request['auth'] = false;

		if (!empty($config['proxy']['host'])) {
			$this->Http->configProxy($config['proxy']);
		}
	}

/**
 * Enable Cache
 *
 * @params mixed $config
 */
	public function enableCache($config = true) {
		$this->setConfig(array('cache' => $config));
	}

/**
 * Next request force update cache
 */
	public function refreshCache() {
		$this->setConfig(array('refresh_cache' => true));
	}

/**
 * set access token
 *
 * @param mixed $oauth_token
 * @param string $oauth_token_secret
 */
	public function setToken($oauth_token, $oauth_token_secret = null) {
		if (is_array($oauth_token) && isset($oauth_token['oauth_token']) && isset($oauth_token['oauth_token_secret'])) {
			$oauth_token_secret = $oauth_token['oauth_token_secret'];
			$oauth_token = $oauth_token['oauth_token'];
		}
		$this->setConfig(compact('oauth_token', 'oauth_token_secret'));
	}

/**
 * get api call remaining
 *
 * @return array
 */
	public function getRatelimit() {
		if (empty($this->Http->response['header'])) {
			return array();
		}

		$headers = array(
			'x-rate-limit-remaining' => true,
			'x-rate-limit-limit' => true,
			'x-rate-limit-reset' => true,
		);

		return array_intersect_key(array_change_key_case($this->Http->response['header'], CASE_LOWER), $headers);
	}

/**
 * Adds in common elements to the request such as the host and extension and
 * OAuth params from config if not set in the request already
 *
 * @param AppModel $model The model the operation is called on. Should have a
 *  request property in the format described in HttpSocket::request
 * @return mixed Depending on what is returned from RestSource::request()
 */
	protected function _request($model) {
		// If auth key is set and not false, fill the request with auth params from
		// config if not already present in the request and set the method to OAuth
		// to trigger HttpSocketOauth to sign the request
		if (array_key_exists('auth', $model->request)
			&& $model->request['auth'] !== false) {

			if (!is_array($model->request['auth'])) {
				$model->request['auth'] = array();
			}
			if (!isset($model->request['auth']['method'])) {
				$model->request['auth']['method'] = 'OAuth';
			}
			$oAuthParams = array(
				'oauth_consumer_key',
				'oauth_consumer_secret',
				'oauth_token',
				'oauth_token_secret',
			);
			foreach ($oAuthParams as $oAuthParam) {
				if (!isset($model->request['auth'][$oAuthParam]) && !empty($this->config[$oAuthParam])) {
					$model->request['auth'][$oAuthParam] = $this->config[$oAuthParam];
				}
			}
			// Set default uri scheme to https
			if (!isset($model->request['uri']['scheme']) && extension_loaded('openssl')) {
				$model->request['uri']['scheme'] = 'https';
			}
		}

		// Set default host, N.B. some API calls use api.twitter.com, in which case
		// they should be set in the individual model call
		if (!isset($model->request['uri']['host'])) {
			$model->request['uri']['host'] = 'api.twitter.com';
		}

		// Append '.json' to path if not already got an extension
		if (!preg_match('/\.(?:json|xml)$/', $model->request['uri']['path'])
			&& !preg_match('!oauth/!i', $model->request['uri']['path'])) {
			$model->request['uri']['path'] .= '.json';
		}

		// Get the response from calling request on the Rest Source (it's parent)
		$response = parent::request($model);

		return $response;
	}

/**
 * Request API and process responce
 *
 * @param TwimAppModel $model
 * @return mixed
 */
	public function request($model) {
		if ($this->_cacheable($model->request)) {
			$this->_setupCache();
		}

		$response = array();

		if ($this->_cacheable($model->request) && !$this->config['refresh_cache']) {
			// get Cache, only GET method
			$response = Cache::read($this->_getCacheKey($model->request), $this->configKeyName);
		}

		if (empty($response)) {
			$response = $this->_request($model);

			if ($this->_cacheable($model->request)) {
				// save Cache, only GET method
				$cache = Cache::write($this->_getCacheKey($model->request), $response, $this->configKeyName);
				$this->config['refresh_cache'] = false;
			}
		}

		return $response;
	}

/**
 * Set proxy settings
 *
 * @param mixed $host Proxy host. Can be an array with settings to authentication class
 * @param integer $port Port. Default 3128.
 * @param string $method Proxy method (ie, Basic, Digest). If empty, disable proxy authentication
 * @param string $user Username if your proxy need authentication
 * @param string $pass Password to proxy authentication
 * @return void
 */
	public function configProxy($host, $port = 3128, $method = null, $user = null, $pass = null) {
		$this->Http->configProxy($host, $port, $method, $user, $pass);
	}

/**
 * get Cache key
 *
 * @param array $params
 * @return stirng
 */
	protected function _getCacheKey($params) {
		return sha1($this->config['oauth_token'] . serialize($params));
	}

/**
 *
 */
	protected function _setupCache() {
		if ($this->config['cache'] && !Cache::isInitialized($this->configKeyName)) {
			if (!is_array($this->config['cache'])) {
				$this->config['cache'] = array(
					'engine' => 'File',
					'duration' => '+5 min',
					'path' => CACHE . 'twitter' . DS,
					'prefix' => 'cake_' . Inflector::underscore($this->configKeyName) . '_',
					'mask' => 0666,
				);
			}

			Cache::config($this->configKeyName, $this->config['cache']);
		}
	}

/**
 * is cacheable
 *
 * @param array $params
 * @return bool
 */
	protected function _cacheable($request) {
		return $this->config['cache'] && strtoupper($request['method']) === 'GET' && !preg_match('!oauth/!i', $request['uri']['path']);
	}

}
