<?php

/**
 * test TwimOauth
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
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');
App::uses('TwimOauth', 'Twim.Model');

/**
 *
 * @property TwimOauth $Oauth
 */
class TwimOauthTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Oauth = ClassRegistry::init('Twim.TwimOauth');
		$this->Oauth->setDataSource($this->mockDatasourceName);
		$this->Oauth->getDataSource()->config['oauth_callback'] = 'http://example.com/oauth_callback';
	}

	public function tearDown() {
		unset($this->Oauth);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testGetRequestToken() {
		$this->Oauth->getDataSource()->expects($this->once())->method('request');
		$this->Oauth->getRequestToken();
		$this->assertSame('oauth/request_token', $this->Oauth->request['uri']['path']);
		$this->assertSame('POST', $this->Oauth->request['method']);
		$this->assertSame(array('oauth_callback' => 'http://example.com/oauth_callback'), $this->Oauth->request['body']);
	}

	public function testGetRequestToken_with_callback_url() {
		$this->Oauth->getDataSource()->expects($this->once())->method('request');
		$this->Oauth->getRequestToken('http://example.com/foo_bar');
		$this->assertSame('oauth/request_token', $this->Oauth->request['uri']['path']);
		$this->assertSame('POST', $this->Oauth->request['method']);
		$this->assertSame(array('oauth_callback' => 'http://example.com/foo_bar'), $this->Oauth->request['body']);
	}

	public function testGetRequestToken_real() {
		$this->Oauth->setDataSource($this->testDatasourceName);
		$result = $this->Oauth->getRequestToken();
		$this->assertNotEmpty($result['oauth_token']);
		$this->assertNotEmpty($result['oauth_token_secret']);
	}

	// =========================================================================

	public function testGetAuthorizeUrl() {
		$this->assertSame('https://api.twitter.com/oauth/authorize?oauth_token=Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik', $this->Oauth->getAuthorizeUrl('Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik'));
	}

	// =========================================================================

	public function testGetAuthenticateUrl() {
		$this->assertSame('https://api.twitter.com/oauth/authenticate?oauth_token=Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik', $this->Oauth->getAuthenticateUrl('Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik'));
	}

	// =========================================================================

	public function testGetAccessToken() {
		$this->Oauth->getDataSource()->expects($this->once())->method('request')->will(
			$this->returnValue(
				'oauth_token=6253282-eWudHldSbIaelX7swmsiHImEL4KinwaGloHANdrY&oauth_token_secret=2EEfA6BG3ly3sR3RjE0IBSnlQu4ZrUzPiYKmrkVU&user_id=6253282&screen_name=twitterapi'
			));
		$oauth_token = 'dummy_token';
		$oauth_verifier = 'dummy_verifier';
		$result = $this->Oauth->getAccessToken(compact('oauth_token', 'oauth_verifier'));
		$this->assertSame('oauth/access_token', $this->Oauth->request['uri']['path']);
		$this->assertSame('POST', $this->Oauth->request['method']);
		$this->assertSame(array(
			'oauth_token' => 'dummy_token',
			'oauth_verifier' => 'dummy_verifier'
			), $this->Oauth->request['auth']);
		$this->assertSame('6253282-eWudHldSbIaelX7swmsiHImEL4KinwaGloHANdrY', $result['oauth_token']);
		$this->assertSame('2EEfA6BG3ly3sR3RjE0IBSnlQu4ZrUzPiYKmrkVU', $result['oauth_token_secret']);
		$this->assertSame('6253282', $result['user_id']);
		$this->assertSame('twitterapi', $result['screen_name']);
		$this->assertSame('6253282-eWudHldSbIaelX7swmsiHImEL4KinwaGloHANdrY', $this->Oauth->getDataSource()->config['oauth_token']);
		$this->assertSame('2EEfA6BG3ly3sR3RjE0IBSnlQu4ZrUzPiYKmrkVU', $this->Oauth->getDataSource()->config['oauth_token_secret']);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testGetAccessToken_real() {
		$this->Oauth->setDataSource($this->testDatasourceName);
		$oauth_token = 'B6gyHD1wS0xI02oPkVekZ5CqOGNEmrQXAVfa8amKc';
		$oauth_verifier = 'sAquV9j08QST9bZcMoCJbQJpR64afO1mpDJfZvik';
		$result = $this->Oauth->getAccessToken(compact('oauth_token', 'oauth_verifier'));
	}

	// =========================================================================

	public function testSetToken() {
		$this->Oauth->setToken('dummy_token', 'dummy_secret');
		$this->assertSame('dummy_token', $this->Oauth->getDataSource()->config['oauth_token']);
		$this->assertSame('dummy_secret', $this->Oauth->getDataSource()->config['oauth_token_secret']);
	}

	public function testSetToken_with_array() {
		$this->Oauth->setToken(array('oauth_token' => 'dummy_token2', 'oauth_token_secret' => 'dummy_secret2'));
		$this->assertSame('dummy_token2', $this->Oauth->getDataSource()->config['oauth_token']);
		$this->assertSame('dummy_secret2', $this->Oauth->getDataSource()->config['oauth_token_secret']);
	}

}
