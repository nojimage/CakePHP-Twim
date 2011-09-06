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
 * @see       https://dev.twitter.com/docs/api/1/get/oauth/authenticate
 * @see       https://dev.twitter.com/docs/api/1/get/oauth/authorize
 * @see       https://dev.twitter.com/docs/api/1/post/oauth/access_token
 * @see       https://dev.twitter.com/docs/api/1/post/oauth/request_token
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

        if (empty($params['oauth_callback']) && !empty($this->getDataSource()->config['oauth_callback'])) {
            $params['oauth_callback'] = $this->getDataSource()->config['oauth_callback'];
        }

        // normalize url
        if (!empty($params['oauth_callback']) && !preg_match('!^https?://!', $params['oauth_callback'])) {
            $params['oauth_callback'] = Router::url($params['oauth_callback'], true);
        }

        if (empty($params['x_auth_access_type']) && !empty($this->getDataSource()->config['x_auth_access_type'])) {
            $params['x_auth_access_type'] = $this->getDataSource()->config['x_auth_access_type'];
        }

        $this->request = array(
            'uri' => array('schema' => 'https', 'path' => 'oauth/request_token'),
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
            'uri' => array('schema' => 'https', 'path' => 'oauth/access_token'),
            'method' => 'POST',
            'auth' => true,
            'body' => $params,
        );

        $result = $this->getDataSource()->request($this);
        parse_str($result, $accessToken);

        $oauth_token = $accessToken['oauth_token'];
        $oauth_token_secret = $accessToken['oauth_token_secret'];
        $this->getDataSource()->setConfig(compact('oauth_token', 'oauth_token_secret'));

        return $accessToken;
    }

    /**
     * 
     */
    public function onError() {

        if (isset($this->response) && is_string($this->response)) {
            App::import('Core', 'Xml');
            $Xml = new Xml($this->response);
            $this->response = $Xml->toArray(false);
            $Xml->__destruct();
            $Xml = null;
            unset($Xml);

            if (isset($this->response['hash'])) {
                $this->response = $this->response['hash'];
            }
        }
        parent::onError();
    }

}