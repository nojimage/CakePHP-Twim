<?php

/**
 * Twitter Authenticate test
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
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');
App::uses('TwitterAuthenticate', 'Twim.Controller/Component/Auth');
App::uses('AppModel', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('TwimOauth', 'Twim.Model');

class TwitterAuthenticateTwitterUser extends AppModel {

	public $alias = 'TwitterUser';

	public $useTable = 'twitter_users';

	public $actsAs = array(
		'Twim.TwitterAuth'
	);

}

/**
 * Test case for TwitterAuthentication
 *
 * @property ComponentCollection $Collection
 * @property TwitterAuthenticate $auth
 * @property CakeResponse $response
 * @property TwimOauth $TwimOauth
 * @property AppModel $TwitterUser
 */
class TwitterAuthenticateTest extends TwimConnectionTestCase {

	public $fixtures = array('plugin.twim.twitter_user');

/**
 * setup
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Collection = $this->getMock('ComponentCollection');
		$this->auth = new TwitterAuthenticate($this->Collection, array(
				'userModel' => 'TwitterUser',
				'datasource' => $this->testDatasourceName,
				'authenticate' => false,
			));
		$this->response = $this->getMock('CakeResponse');

		$this->TwimOauth = $this->getMock('TwimOauth', array('getRequestToken', 'getAccessToken'));
		$this->TwimOauth->setDatasource($this->testDatasourceName);
		ClassRegistry::addObject('TwimOauth', $this->TwimOauth);

		$this->TwitterUser = ClassRegistry::init('TwitterAuthenticateTwitterUser');
		ClassRegistry::addObject('TwitterUser', $this->TwitterUser);
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Collection, $this->auth, $this->response, $this->TwimOauth, $this->TwitterUser);
		parent::tearDown();
		ob_flush();
	}

/**
 * test applying settings in the constructor
 *
 * @return void
 */
	public function testConstructor() {
		$object = new TwitterAuthenticate($this->Collection, array(
				'userModel' => 'SomeTwitterUser',
				'datasource' => $this->mockDatasourceName,
				'authenticate' => true,
			));
		$this->assertEquals('SomeTwitterUser', $object->settings['userModel']);
		$this->assertEquals($this->mockDatasourceName, $object->settings['datasource']);
		$this->assertEquals(true, $object->settings['authenticate']);
	}

/**
 * test applying settings in the constructor
 *
 * @return void
 */
	public function testConstructorDefault() {
		$object = new TwitterAuthenticate($this->Collection, array());
		$this->assertEquals(false, $object->settings['userModel']);
		$this->assertEquals('twitter', $object->settings['datasource']);
		$this->assertEquals(false, $object->settings['authenticate']);
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoData() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array();
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateLoginRequest() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array('Twitter' => array('login' => true));

		$this->TwimOauth->expects($this->once())
			->method('getRequestToken')
			->will($this->returnValue(array(
					'oauth_token' => 'dummy_token',
					'oauth_token_secret' => 'dummy_secret',
					'oauth_callback_confirmed' => 'true',
				)));
		$this->response->expects($this->once())
			->method('header')
			->with('Location', 'https://api.twitter.com/oauth/authorize?oauth_token=dummy_token&oauth_token_secret=dummy_secret&oauth_callback_confirmed=true');

		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateGetAccessToken() {
		$request = new CakeRequest('posts/index', false);
		$request->query = array('oauth_token' => 'dummy_request_token', 'oauth_verifier' => 'dummy_verifier');
		$this->TwimOauth->expects($this->once())
			->method('getAccessToken')
			->with(array('oauth_token' => 'dummy_request_token', 'oauth_verifier' => 'dummy_verifier'))
			->will($this->returnValue(array(
					'user_id' => '12345',
					'screen_name' => 'dummy_name',
					'oauth_token' => 'dummy_access_token',
					'oauth_token_secret' => 'dummy_secret',
				)));

		$result = $this->auth->authenticate($request, $this->response);
		$this->assertEquals(12345, $result['id']);
		$this->assertSame('dummy_name', $result['username']);
		$this->assertSame('dummy_access_token', $result['oauth_token']);
		$this->assertSame('dummy_secret', $result['oauth_token_secret']);
		$this->assertArrayHasKey('created', $result);
	}

}
