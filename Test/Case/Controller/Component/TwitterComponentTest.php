<?php

/**
 * Twitter API Component Test Case
 */
App::uses('TwitterComponent', 'Twim.Controller/Component');
App::uses('Controller', 'Controller');
App::uses('Model', 'Model');

/**
 * @property TwitterComponent $Twitter
 */
class TwitterComponentTestController extends Controller {

	public $uses = array();

	public $components = array('Twim.Twitter' => array('datasource' => 'test_twitter_component'));

	public $helpers = array();

	public $stoped = false;

	public $status = 200;

	public $headers = array();

	public $redirectUrl = null;

	public function _stop($status = 0) {
		$this->stoped = $status;
	}

	public function redirect($url, $status = null, $exit = true) {
		$this->status = $status;
		$this->redirectUrl = $url;
	}

	public function header($status) {
		$this->headers[] = $status;
	}

}

class TwitterComponentTestUser extends Model {

	public $name = 'TwitterUser';

	public $alias = 'TwitterUser';

	public $useTable = false;

}

/**
 *
 * @property TwitterComponentTestController $Controller
 */
class TwitterComponentTest extends CakeTestCase {

	static function setUpBeforeClass() {
		ConnectionManager::create('test_twitter_component', array(
			'datasource' => 'Twim.TwimSource',
			'oauth_consumer_key' => 'cvEPr1xe1dxqZZd1UaifFA',
			'oauth_consumer_secret' => 'gOBMTs7Rw4Z3p5EhzqBey8ousRTwNDvreJskN8Z60',
		));

		ConnectionManager::create('fake_twitter', array(
			'datasource' => 'Twim.TwimSource',
			'oauth_consumer_key' => 'cvEPr1xe1dxqZZd1UaifFA',
			'oauth_consumer_secret' => 'gOBMTs7Rw4Z3p5EhzqBey8ousRTwNDvreJskN8Z60',
		));
	}

	static function tearDownAfterClass() {
		ConnectionManager::drop('test_twitter_component');
		ConnectionManager::drop('fake_twitter');
	}

	public function setUp() {
		parent::setUp();
		$this->Controller = new TwitterComponentTestController(null);
		$this->Controller->constructClasses();
		$this->Controller->startupProcess();
	}

	public function tearDown() {
		unset($this->Controller);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testConstruct() {
		$this->assertIsA($this->Controller->Twitter, 'TwitterComponent');
		$this->assertEquals('test_twitter_component', $this->Controller->Twitter->settings['datasource']);
		$this->assertEquals('oauth_token', $this->Controller->Twitter->settings['fields']['oauth_token']);
		$this->assertEquals('oauth_token_secret', $this->Controller->Twitter->settings['fields']['oauth_token_secret']);
		$this->assertIsA($this->Controller->Twitter->Session, 'SessionComponent');
		$this->assertIsA($this->Controller->Twitter->TwimOauth, 'TwimOauth');
	}

	public function testConstruct_additionalParameter() {
		$this->Controller->Components->unload('Twim.Twitter');
		$this->Controller->Components->load('Twim.Twitter', array(
			'datasource' => 'fake_twitter',
			'fields' => array('oauth_token' => 'access_token', 'oauth_token_secret' => 'access_token_secret')
		));
		$this->Controller->Components->init($this->Controller);
		$this->assertEquals('fake_twitter', $this->Controller->Twitter->settings['datasource']);
		$this->assertEquals('access_token', $this->Controller->Twitter->settings['fields']['oauth_token']);
		$this->assertEquals('access_token_secret', $this->Controller->Twitter->settings['fields']['oauth_token_secret']);
	}

	public function testInitialize_with_AuthComponent() {
		Configure::write('Routing.prefixes', array('admin'));
		$this->Controller->Auth = new Object();
		$this->Controller->Twitter->initialize($this->Controller);
		$this->assertIdentical(array('plugin' => 'twim', 'controller' => 'oauth', 'action' => 'login', 'admin' => false), $this->Controller->Auth->loginAction);
	}

	// =========================================================================

	public function testGetTwimSource() {
		$this->assertIsA($this->Controller->Twitter->getTwimSource(), 'TwimSource');
		$this->assertEqual('test_twitter_component', $this->Controller->Twitter->getTwimSource()->configKeyName);
	}

	// =========================================================================

	public function testGetAuthorizedUrl() {
		$callback = Router::url('/twim/oauth/callback', true);
		$result = $this->Controller->Twitter->getAuthorizeUrl($callback);
		$this->assertPattern('!https://api\.twitter\.com/oauth/authorize\?oauth_token=.+!', $result);
	}

	// =========================================================================

	public function testGetAuthenticateUrl() {
		$callback = Router::url('/twim/oauth/callback', true);
		$result = $this->Controller->Twitter->getAuthenticateUrl($callback);
		$this->assertPattern('!https://api\.twitter\.com/oauth/authenticate\?oauth_token=.+!', $result);

		$this->authenticateUrl = $result;
	}

	// =========================================================================

	public function testConnect() {
		$this->Controller->Twitter->connect();
		$this->assertPattern('!https://api\.twitter\.com/oauth/authenticate\?oauth_token=.+!', $this->Controller->redirectUrl);
	}

	public function testConnect_authorize() {
		$this->Controller->request->named['authorize'] = 'true';
		$this->Controller->Twitter->connect();
		$this->assertPattern('!https://api\.twitter\.com/oauth/authorize\?oauth_token=.+!', $this->Controller->redirectUrl);
	}

	// =========================================================================

	public function testGetAccessToken_noToken() {
		$result = $this->Controller->Twitter->getAccessToken();
		$this->assertFalse($result);
	}

	/**
	 *
	 * @expectedException RuntimeException 
	 * @expectedExceptionMessage Invalid / expired Token
	 */
	public function testGetAccessToken_invalid() {
		$result = array();
		$this->Controller->request->query['oauth_token'] = 'invalid token';
		$this->Controller->request->query['oauth_verifier'] = 'invalid verifier';
		$result = $this->Controller->Twitter->getAccessToken();
	}

	public function testGetAccessToken() {
		$this->markTestSkipped('input right token/verifier');
		$result = array();
		$this->Controller->request->query['oauth_token'] = 'vkwlQH1uLWWahUNa7PNE6RbBTYGotugP9wh3NSoT0';
		$this->Controller->request->query['oauth_verifier'] = 'DUWU7DpwCGYNgKbq1B9Pf3uhwVDLyv9XvTP3T3DVAo';
		$result = $this->Controller->Twitter->getAccessToken();

		$this->assertIsA($result['oauth_token'], 'String');
		$this->assertIsA($result['oauth_token_secret'], 'String');
		$this->assertIsA($result['user_id'], 'String');
		$this->assertIsA($result['screen_name'], 'String');
	}

	// =========================================================================

	public function testSetToken_null_params() {
		$result = $this->Controller->Twitter->setToken('');
		$this->assertEqual('', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
		$this->assertEqual('', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
	}

	public function testSetToken_with_array() {
		$result = $this->Controller->Twitter->setToken(array('oauth_token' => 'dummy_token', 'oauth_token_secret' => 'dummy_secret'));
		$this->assertEqual('dummy_token', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
		$this->assertEqual('dummy_secret', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
	}

	public function testSetToken() {
		$result = $this->Controller->Twitter->setToken('dummy_token2', 'dummy_secret2');
		$this->assertEqual('dummy_token2', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
		$this->assertEqual('dummy_secret2', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
	}

	public function testSetTokenByUser() {
		$user = array(
			'User' => array(
				'oauth_token' => 'dummy_token',
				'oauth_token_secret' => 'dummy_secret',
			));
		$result = $this->Controller->Twitter->setTokenByUser($user);
		$this->assertEqual('dummy_token', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
		$this->assertEqual('dummy_secret', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
	}

	public function testSetTokenByUser_change_field_name() {
		$this->Controller->Twitter->settings['fields']['oauth_token'] = 'accsess_token';
		$this->Controller->Twitter->settings['fields']['oauth_token_secret'] = 'accsess_token_secret';

		$user = array(
			'User' => array(
				'accsess_token' => 'dummy_token2',
				'accsess_token_secret' => 'dummy_secret2',
			));
		$result = $this->Controller->Twitter->setTokenByUser($user);
		$this->assertEqual('dummy_token2', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
		$this->assertEqual('dummy_secret2', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
	}

	// =========================================================================

	public function testSaveToUser() {
		$this->Controller->Auth = $this->getMock('Object', array('getModel'));
		$this->Controller->Auth->userModel = 'TwitterComponentMockUser';

		$model = $this->getMock('TwitterComponentTestUser', array('save', 'createSaveDataByToken'), array(), 'TwitterComponentMockUser');
		$this->Controller->Auth->expects($this->once())->method('getModel')->will($this->returnValue($model));

		$token = array(
			'user_id' => '123456789',
			'screen_name' => 'dummy_user',
			'oauth_token' => 'dummy token',
			'oauth_token_secret' => 'dummy secret token',
		);
		$saveData = array(
			'TwitterUser' => array(
				'id' => '123456789',
				'username' => 'dummy_user',
				'oauth_token' => 'dummy token',
				'oauth_token_secret' => 'dummy secret token',
				'password' => 'ae9277742549f954cb43408b44fd3610a5b5e9db',
			),
		);

		$model->expects($this->once())->method('createSaveDataByToken')->with($token)->will($this->returnValue($saveData));
		$model->expects($this->once())->method('save')->with($saveData)->will($this->returnValue($saveData));

		$result = $this->Controller->Twitter->saveToUser($token);
		$this->assertIdentical($result, $saveData);
	}

}
