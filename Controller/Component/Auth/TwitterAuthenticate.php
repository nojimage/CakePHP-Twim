<?php

/**
 * Twitter Authenticate
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
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::uses('TwimOauth', 'Twim.Model');

/**
 * TwitterAuthenticate for AuthComponent
 *
 * @author    nojimage <nojimage at gmail.com>
 */
class TwitterAuthenticate extends BaseAuthenticate {

	public $settings = array(
		'userModel' => false,
		'datasource' => 'twitter',
		'authenticate' => false,
	);

/**
 *
 * @param CakeRequest $request
 * @param CakeResponse $response
 * @return boolean
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		$oauth = ClassRegistry::init('Twim.TwimOauth');
		/* @var $oauth TwimOauth */

		if (!empty($request->data['Twitter']['login'])) {
			// redirect to twitter
			$requestToken = $oauth->getRequestToken();
			$redirectUrl = $this->settings['authenticate'] ? $oauth->getAuthenticateUrl($requestToken) : $oauth->getAuthorizeUrl($requestToken);
			$response->header('Location', $redirectUrl);
		} elseif (isset($request->query['oauth_token']) && isset($request->query['oauth_verifier'])) {
			// get access token
			$verifier = array_intersect_key($request->query, array('oauth_token' => true, 'oauth_verifier' => true));
			$accessToken = $oauth->getAccessToken($verifier);

			if ($this->settings['userModel'] === false) {
				return $accessToken;
			}

			// save user data
			return $this->saveToModel($accessToken);
		}

		return false;
	}

/**
 * save token to User model
 *
 * @param array $token
 * @return array User record
 */
	public function saveToModel(array $token) {
		$model = ClassRegistry::init($this->settings['userModel']);
		/* @var $model TwitterUser */

		$data = array();

		// save to database
		if (method_exists($model, 'createSaveDataByToken') || $model->Behaviors->hasMethod('createSaveDataByToken')) {
			$data = $model->createSaveDataByToken($token);
		} else {
			$data = $token;
		}

		if ($result = $model->save($data)) {
			$data = $model->read(null, $result[$model->alias][$model->primaryKey]);
			return $data[$model->alias];
		}

		throw new Exception(__d('twim', 'The user could not be saved'));
	}

}
