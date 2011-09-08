<?php

/**
 * Twim Oauth Controller
 *
 * PHP version 5
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   1.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2011 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   twim
 * @since     File available since Release 1.0
 * */

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

        if (!empty($this->params['named']['datasource'])) {
            $linkOptions['datasource'] = $this->params['named']['datasource'];
        }

        if (!empty($this->params['named']['authenticate'])) {
            $linkOptions['authenticate'] = $this->params['named']['authenticate'];
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
        $this->Session->setFlash(__d('twim', 'Signed out', true));
        $this->redirect($this->Auth->logout());
    }

    /**
     * OAuth callback
     */
    public function callback($dataSource = null) {

        $this->Twitter->TwimOauth->setDataSource($dataSource);

        try {

            $useAuth = isset($this->Auth);

            // check return token
            if (empty($this->params['url']['oauth_token']) || empty($this->params['url']['oauth_verifier'])) {
                throw new Exception(__d('twim', 'invalid request.', true));
            }

            // get access token
            $token = $this->Twitter->getAccessToken();

            if (!$useAuth) {
                $this->Session->write('TwitterUser', $token);
                $this->redirect('/');
            }

            $loginRedirect = $this->Auth->redirect();
            $data = $this->Twitter->saveToUser($token);
            $this->Auth->login($data);
            // Redirect
            if (ini_get('session.referer_check') && env('HTTP_REFERER')) {
                $this->flash(sprintf(__d('twim', 'Redirect to %s', true), Router::url($loginRedirect, true) . ini_get('session.referer_check')), $loginRedirect, 0);
                return;
            }

            $this->redirect($loginRedirect);
        } catch (Exception $e) {
            $this->Twitter->deleteCachedAuthorizeUrl();
            $this->flash(__d('twim', 'Authorization Error: ', true) . $e->getMessage(), array('action' => 'login'), 5);
            return;
        }
    }

}
