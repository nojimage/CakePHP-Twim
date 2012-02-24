<?php

/**
 * Twim Oauth Controller
 *
 * CakePHP 2.0
 * PHP version 5
 *
 * Copyright 2012, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   2.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2012 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   Twim
 * @since     File available since Release 1.0
 */

/**
 * @property AuthComponent $Auth
 * @property TwitterComponent $Twitter
 */
class OauthController extends AppController {

	public $uses = array();

	public $components = array('Twim.Twitter');

	public $helpers = array('Html', 'Form', 'Js', 'Twim.Twitter');

	/**
	 *
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'connect', 'callback');
	}

	/**
	 * login action
	 */
	public function login() {
		$linkOptions = array();

		if (!empty($this->request->named['datasource'])) {
			$linkOptions['datasource'] = $this->request->named['datasource'];
		}

		if (!empty($this->request->named['authenticate'])) {
			$linkOptions['authenticate'] = $this->request->named['authenticate'];
		}

		$this->set(compact('linkOptions'));
	}

	/**
	 * redirect twitter authorize page
	 */
	public function connect() {
		$this->Twitter->connect();
	}

	/**
	 * logout action
	 */
	public function logout() {
		$this->Session->setFlash(__d('twim', 'Signed out'));
		$this->Session->delete('TwitterAuth');
		$this->redirect($this->Auth->logout());
	}

	/**
	 * OAuth callback
	 */
	public function callback($dataSource = null) {
		$this->Twitter->TwimOauth->setDataSource($dataSource);
		$this->Twitter->deleteCachedAuthorizeUrl();

		// check return token
		if (empty($this->request->query['oauth_token']) || empty($this->request->query['oauth_verifier'])) {
			throw new InvalidArgumentException(__d('twim', 'invalid request.'));
		}

		if (!$this->Components->enabled('Auth')) {
			// only set token to session, if AuthComponent not active.
			// get access token
			$token = $this->Twitter->getAccessToken();
			$this->Session->write('TwitterAuth', $token);
			$this->redirect('/');
		}

		if ($this->Auth->login()) {
			$loginRedirect = $this->Auth->redirect();
			// Redirect
			if (ini_get('session.referer_check') && env('HTTP_REFERER')) {
				$this->flash(sprintf(__d('twim', 'Redirect to %s'), Router::url($loginRedirect, true) . ini_get('session.referer_check')), $loginRedirect, 0);
				return;
			}
			$this->redirect($loginRedirect);
		}

		$this->redirect(array('action' => 'login'));
	}

}
