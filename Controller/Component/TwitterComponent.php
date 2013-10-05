<?php

/**
 * TwitterKit Twitter Component
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
App::uses('Component', 'Controller');

/**
 * @property AppController $Controller
 * @property SessionComponent $Session
 * @property TwimOauth $TwimOauth
 */
class TwitterComponent extends Component {

	public $name = 'Twitter';

	public $components = array('Session');

	public $defaults = array(
		'datasource' => 'twitter',
		'fields' => array(
			'oauth_token' => 'oauth_token',
			'oauth_token_secret' => 'oauth_token_secret',
		),
	);

/**
 * default: 5min
 *
 * @var int
 */
	CONST OAUTH_URL_EXPIRE = 300;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->settings = Set::merge($this->defaults, $settings);
		$this->TwimOauth = ClassRegistry::init('Twim.TwimOauth');
		$this->TwimOauth->setDataSource($this->settings['datasource']);
	}

/**
 *
 * @param AppController $controller
 * @param array         $settings
 */
	public function initialize(Controller $controller) {
		$this->Controller = $controller;
		if ($this->Controller->Components->attached('Auth')) {
			$url = array('plugin' => 'twim', 'controller' => 'oauth', 'action' => 'login');
			if (Configure::read('Routing.prefixes')) {
				foreach (Configure::read('Routing.prefixes') as $prefix) {
					$url[$prefix] = false;
				}
			}
			$this->Controller->Components->Auth->loginAction = $url;

			// register authenticate
			if (!isset($this->Controller->Components->Auth->authenticate['Twim.Twitter'])
				&& in_array('Twim.Twitter', $this->Controller->Components->Auth->authenticate)) {
				$this->Controller->Components->Auth->authenticate[] = 'Twim.Twitter';
			}
		}
	}

/**
 *
 * @param AppController $controller
 */
	public function startup(Controller $controller) {
		$this->Controller = $controller;
	}

/**
 * get DataSource Object
 *
 * @return TwimSource
 */
	public function getTwimSource() {
		return $this->TwimOauth->getDataSource();
	}

/**
 * make OAuth Authorize URL
 *
 * @param string $callbackUrl
 * @param bool   $useCache
 * @return string authorize url
 */
	public function getAuthorizeUrl($callbackUrl = null, $useCache = false) {
		// -- check Session
		if ($useCache && $this->hasCachedUrl('authorize')) {
			return $this->getCachedUrl('authorize');
		}

		// -- request token
		$requestToken = $this->TwimOauth->getRequestToken($callbackUrl);
		$url = $this->TwimOauth->getAuthorizeUrl($requestToken);

		// -- set cache
		if ($useCache) {
			$this->setCachedUrl('authorize', $url);
		}

		return $url;
	}

/**
 * make OAuth Authenticate URL
 *
 * @param string $callbackUrl
 * @param bool   $useCache
 * @return string authorize_url
 */
	public function getAuthenticateUrl($callbackUrl = null, $useCache = false) {
		// -- check Session
		if ($useCache && $this->hasCachedUrl('authenticate')) {
			return $this->getCachedUrl('authenticate');
		}

		// -- request token
		$requestToken = $this->TwimOauth->getRequestToken($callbackUrl);
		$url = $this->TwimOauth->getAuthenticateUrl($requestToken);

		// -- set cache
		if ($useCache) {
			$this->setCachedUrl('authenticate', $url);
		}
		return $url;
	}

/**
 * redirect Twitter authorize page
 */
	public function connect() {
		$authorize = true;
		$dataSource = $authenticate = false;

		if (!empty($this->Controller->request->named['datasource'])) {
			$dataSource = $this->Controller->request->named['datasource'];
		}

		if (!empty($this->Controller->request->named['authenticate'])) {
			$authenticate = $this->Controller->request->named['authenticate'];
		}

		if (!empty($this->Controller->request->named['authorize'])) {
			$authorize = $this->Controller->request->named['authorize'];
		}

		if ($dataSource !== false) {
			$this->TwimOauth->setDataSource($dataSource);
		}

		if ($authenticate || !$authorize) {
			$this->Controller->redirect($this->getAuthenticateUrl());
		} else {
			$this->Controller->redirect($this->getAuthorizeUrl());
		}
	}

/**
 * get OAuth Access Token
 *
 * @return array|false
 */
	public function getAccessToken() {
		// remove authorize/authenticate url from session
		$this->deleteCachedAuthorizeUrl();

		if (empty($this->Controller->request->query['oauth_token']) || empty($this->Controller->request->query['oauth_verifier'])) {
			return false;
		}

		$oauth_token = $this->Controller->request->query['oauth_token'];
		$oauth_verifier = $this->Controller->request->query['oauth_verifier'];

		$accessToken = $this->TwimOauth->getAccessToken(compact('oauth_token', 'oauth_verifier'));

		return $accessToken;
	}

/**
 * set OAuth Access Token
 *
 * @param mixed $token
 * @param string $secret
 * @return true|false
 */
	public function setToken($token, $secret = null) {
		if (is_array($token) && !empty($token[$this->settings['fields']['oauth_token']]) && !empty($token[$this->settings['fields']['oauth_token_secret']])) {
			$secret = $token[$this->settings['fields']['oauth_token_secret']];
			$token = $token[$this->settings['fields']['oauth_token']];
		}

		return $this->TwimOauth->setToken($token, $secret);
	}

/**
 * set OAuth Access Token by Authorized User
 *
 * @param  array $user
 */
	public function setTokenByUser($user = null) {
		$modelName = 'User';

		if (empty($user) && !empty($this->Controller->Auth) && is_object($this->Controller->Auth)) {
			$user = $this->Controller->Auth->user();
			list($plugin, $modelName) = pluginSplit($this->Controller->Auth->userModel);
		}

		return $this->setToken($user[$modelName]);
	}

/**
 * save token to User model
 *
 * @param array $token
 * @return array User record
 * @deprecated
 */
	public function saveToUser($token) {
		$model = $this->Controller->Auth->getModel();
		/* @var $model TwitterUser */
		$data = array();

		// save to database
		if (method_exists($model, 'createSaveDataByToken') || in_array('createSaveDataByToken', $model->Behaviors->methods())) {
			$data = $model->createSaveDataByToken($token);
		} else {
			$data = $token;
		}

		if ($result = $model->save($data)) {
			return $result;
		}

		throw new Exception(__d('twim', 'The user could not be saved'));
	}

/**
 * delete Authorize/Authenticate url from Session
 */
	public function deleteCachedAuthorizeUrl() {
		$this->Session->delete($this->getCachedUrlSessionKey('authorize'));
		$this->Session->delete($this->getCachedUrlSessionKey('authenticate'));
		return $this;
	}

/**
 *
 * @param string $type
 * @return string
 */
	public function getCachedUrlSessionKey($type) {
		return 'Twim.' . $this->TwimOauth->getDataSource()->configKeyName . '.' . $type;
	}

/**
 *
 * @param string $type
 * @return bool
 */
	public function hasCachedUrl($type = 'authorize') {
		$key = $this->getCachedUrlSessionKey($type);
		if ($this->Session->check($key) && $this->Session->check($key . '.url') && time() <= $this->Session->read($key . '.expire')) {
			return true;
		}
		return false;
	}

/**
 *
 * @param string $type
 * @return string
 */
	public function getCachedUrl($type = 'authorize') {
		if ($this->hasCachedUrl($type)) {
			return $this->Session->read($this->getCachedUrlSessionKey($type) . '.url');
		}
		return false;
	}

/**
 *
 * @param string $type
 * @param string $url
 * @return string url
 */
	public function setCachedUrl($type = 'authorize', $url = '') {
		$key = $this->getCachedUrlSessionKey($type);
		$this->Session->write($key . '.url', $url);
		$this->Session->write($key . '.expire', time() + self::OAUTH_URL_EXPIRE);
		return $url;
	}

}
