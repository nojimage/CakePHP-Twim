<?php

/**
 * for Oauth API
 *
 * PHP versions 5
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
 * @since   　File available since Release 1.0
 * @link      https://dev.twitter.com/docs/api/1/get/oauth/authenticate
 * @link      https://dev.twitter.com/docs/api/1/get/oauth/authorize
 * @link      https://dev.twitter.com/docs/api/1/post/oauth/access_token
 * @link      https://dev.twitter.com/docs/api/1/post/oauth/request_token
 *
 */
class TwimOauth extends TwimAppModel {

    /**
     * get OAuth Request Token
     *
     * @param array $params 
     *      oauth_callback: url
     *      x_auth_access_type: read or write
     * @return string
     */
    public function getRequestToken($params = array()) {

        if (is_string($params)) {
            $params = array('oauth_callback' => $params);
        }

        $config = $this->getDataSource()->config;

        if (empty($params['oauth_callback']) && !empty($config['oauth_callback'])) {
            $params['oauth_callback'] = $config['oauth_callback'];
        }

        // normalize url
        if (!empty($params['oauth_callback']) && !preg_match('!^https?://!', $params['oauth_callback'])) {
            $params['oauth_callback'] = Router::getInstance()->url($params['oauth_callback'], true);
        }

        if (empty($params['x_auth_access_type']) && !empty($config['x_auth_access_type'])) {
            $params['x_auth_access_type'] = $config['x_auth_access_type'];
        }

        $this->request = array(
            'uri' => array('scheme' => 'https', 'path' => 'oauth/request_token'),
            'method' => 'POST',
            'auth' => true,
            'body' => $params,
        );

        $result = $this->getDataSource()->request($this);
        parse_str($result, $requestToken);
        return $requestToken;
    }

    /**
     * get OAuth authorize url
     *
     * @param array or string $requestToken
     * @return string 
     */
    public function getAuthorizeUrl($requestToken) {
        if (is_string($requestToken)) {
            $requestToken = array('oauth_token' => $requestToken);
        }
        return 'https://api.twitter.com/oauth/authorize?' . http_build_query($requestToken);
    }

    /**
     * get OAuth authenticate url
     *
     * @param array or string $requestToken
     * @return string 
     */
    public function getAuthenticateUrl($requestToken) {
        if (is_string($requestToken)) {
            $requestToken = array('oauth_token' => $requestToken);
        }
        return 'https://api.twitter.com/oauth/authenticate?' . http_build_query($requestToken);
    }

    /**
     * get OAuth Access Token
     *
     * @param array $params 
     *      oauth_token: 
     *      oauth_verifier: 
     *      x_auth_password: 
     *      x_auth_username: 
     *      x_auth_mode: 
     * @return string
     */
    public function getAccessToken($params = array()) {

        $this->request = array(
            'uri' => array('scheme' => 'https', 'path' => 'oauth/access_token'),
            'method' => 'POST',
            'auth' => array(),
        );
        foreach (array('oauth_token', 'oauth_verifier') as $key) {
            if (!empty($params[$key])) {
                $this->request['auth'][$key] = $params[$key];
            }
        }

        $result = $this->getDataSource()->request($this);
        parse_str($result, $accessToken);

        $this->setToken($accessToken);

        return $accessToken;
    }

    /**
     * set access token
     *
     * @param mixed $oauth_token
     * @param string $oauth_token_secret
     */
    public function setToken($oauth_token, $oauth_token_secret = null) {
        $this->getDataSource()->setToken($oauth_token, $oauth_token_secret);
    }

    /**
     * 
     */
    public function onError() {

        if (isset($this->response) && is_string($this->response)) {
            if (preg_match('/<\?xml /', $this->response)) {
                App::import('Core', 'Xml');
                $Xml = new Xml($this->response);
                $this->response = $Xml->toArray(false);
                $Xml->__destruct();
                $Xml = null;
                unset($Xml);

                if (isset($this->response['hash'])) {
                    $this->response = $this->response['hash'];
                }
            } else {
                $this->response = array('error' => $this->response);
            }
        }
        parent::onError();
    }

}