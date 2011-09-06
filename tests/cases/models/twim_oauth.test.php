<?php

/**
 * test TwimOauth
 *
 * PHP versions 5
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @version   1.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2011 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link    　http://php-tips.com/
 * @since   　File available since Release 1.0
 *
 */
App::import('Model', 'Twim.TwimOauth');
App::import('Datasource', array('Twim.TwimSource'));

class TestTwimOauth extends TwimOauth {

    public $alias = 'TwimOauth';
    public $useDbConfig = 'test_twitter';

}

Mock::generatePartial('TwimSource', 'MockTwimOauthTwimSource', array('request'));

/**
 *
 * @property TwimOauth $Oauth
 */
class TwimOauthTestCase extends CakeTestCase {

    public function startTest() {
        ConnectionManager::create('test_twitter', array(
            'datasource' => 'MockTwimOauthTwimSource',
        ));

        $this->Oauth = ClassRegistry::init('Twim.TestTwimOauth');
        $this->_dsConfig = $this->Oauth->getDataSource()->config;
        $this->Oauth->getDataSource()->config['oauth_callback'] = 'http://example.com/oauth_callback';
    }

    public function endTest() {
        $this->Oauth->getDataSource()->setConfig($this->_dsConfig);
        unset($this->Oauth);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function testGetRequestToken() {
        $this->Oauth->getDataSource()->expectOnce('request');
        $this->Oauth->getRequestToken();
        $this->assertIdentical($this->Oauth->request['uri']['path'], 'oauth/request_token');
        $this->assertIdentical($this->Oauth->request['method'], 'POST');
        $this->assertIdentical($this->Oauth->request['body'], array('oauth_callback' => 'http://example.com/oauth_callback'));
    }

    public function testGetRequestToken_with_callback_url() {
        $this->Oauth->getDataSource()->expectOnce('request');
        $this->Oauth->getRequestToken('http://example.com/foo_bar');
        $this->assertIdentical($this->Oauth->request['uri']['path'], 'oauth/request_token');
        $this->assertIdentical($this->Oauth->request['method'], 'POST');
        $this->assertIdentical($this->Oauth->request['body'], array('oauth_callback' => 'http://example.com/foo_bar'));
    }

    public function testGetRequestToken_real() {
        ClassRegistry::flush();
        $this->Oauth = ClassRegistry::init('Twim.TwimOauth');
        $result = $this->Oauth->getRequestToken();
        $this->assertTrue(isset($result['oauth_token']));
        $this->assertTrue(isset($result['oauth_token_secret']));
    }

    // =========================================================================
    public function testGetAuthorizeUrl() {
        $this->assertIdentical('https://api.twitter.com/oauth/authorize?oauth_token=Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik', $this->Oauth->getAuthorizeUrl('Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik'));
    }

    // =========================================================================
    public function testGetAuthenticateUrl() {
        $this->assertIdentical('https://api.twitter.com/oauth/authenticate?oauth_token=Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik', $this->Oauth->getAuthenticateUrl('Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik'));
    }

    // =========================================================================
    public function testGetAccessToken() {
        $this->Oauth->getDataSource()->expectOnce('request');
        $this->Oauth->getDataSource()->setReturnValue('request', 'oauth_token=6253282-eWudHldSbIaelX7swmsiHImEL4KinwaGloHANdrY&oauth_token_secret=2EEfA6BG3ly3sR3RjE0IBSnlQu4ZrUzPiYKmrkVU&user_id=6253282&screen_name=twitterapi');
        $oauth_token = 'dummy_token';
        $oauth_verifier = 'dummy_verifier';
        $result = $this->Oauth->getAccessToken(compact('oauth_token', 'oauth_verifier'));
        $this->assertIdentical($this->Oauth->request['uri']['path'], 'oauth/access_token');
        $this->assertIdentical($this->Oauth->request['method'], 'POST');
        $this->assertIdentical($this->Oauth->request['body'], array(
            'oauth_token' => 'dummy_token',
            'oauth_verifier' => 'dummy_verifier',
        ));
        $this->assertIdentical('6253282-eWudHldSbIaelX7swmsiHImEL4KinwaGloHANdrY', $result['oauth_token']);
        $this->assertIdentical('2EEfA6BG3ly3sR3RjE0IBSnlQu4ZrUzPiYKmrkVU', $result['oauth_token_secret']);
        $this->assertIdentical('6253282', $result['user_id']);
        $this->assertIdentical('twitterapi', $result['screen_name']);
        $this->assertIdentical('6253282-eWudHldSbIaelX7swmsiHImEL4KinwaGloHANdrY', $this->Oauth->getDataSource()->config['oauth_token']);
        $this->assertIdentical('2EEfA6BG3ly3sR3RjE0IBSnlQu4ZrUzPiYKmrkVU', $this->Oauth->getDataSource()->config['oauth_token_secret']);
    }

    public function testGetAccessToken_real() {
        ClassRegistry::flush();
        $this->Oauth = ClassRegistry::init('Twim.TwimOauth');
        $oauth_token = 'B6gyHD1wS0xI02oPkVekZ5CqOGNEmrQXAVfa8amKc';
        $oauth_verifier = 'sAquV9j08QST9bZcMoCJbQJpR64afO1mpDJfZvik';
        try {
            $result = $this->Oauth->getAccessToken(compact('oauth_token', 'oauth_verifier'));
        } catch (Exception $e) {
            $this->assertIdentical('Invalid / expired Token', $e->getMessage());
        }
    }

}
