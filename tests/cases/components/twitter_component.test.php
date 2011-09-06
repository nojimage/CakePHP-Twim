<?php

/**
 * Twitter API Component Test Case
 */
App::import('Component', 'Twim.Twitter');

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

    function _stop($status = 0) {
        $this->stoped = $status;
    }

    function redirect($url, $status = null, $exit = true) {
        $this->status = $status;
    }

    function header($status) {
        $this->headers[] = $status;
    }

}

class TestTwitterComponent extends TwitterComponent {
    
}

/**
 * @author nojima
 *
 * @property TwitterComponentTestController $Controller
 */
class TwitterComponentTestCase extends CakeTestCase {

    function startTest() {
        $this->Controller = new TwitterComponentTestController();
        $this->Controller->constructClasses();
        $this->Controller->Component->initialize($this->Controller);
    }

    function endTest() {
        unset($this->Controller);
        ClassRegistry::flush();
    }

    // =========================================================================
    function testInitialize() {
        $this->assertIsA($this->Controller->Twitter, 'TwitterComponent');
        $this->assertEqual('test_twitter_component', $this->Controller->Twitter->settings['datasource']);
        $this->assertEqual('oauth_token', $this->Controller->Twitter->settings['fields']['oauth_token']);
        $this->assertEqual('oauth_token_secret', $this->Controller->Twitter->settings['fields']['oauth_token_secret']);
        $this->assertIsA($this->Controller->Twitter->Session, 'SessionComponent');
        $this->assertIsA($this->Controller->Twitter->TwimOauth, 'TwimOauth');
    }

    function testInitialize_additionalParameter() {
        $this->Controller->Twitter->initialize(null, array(
            'datasource' => 'fake_twitter',
            'fields' => array('oauth_token' => 'access_token', 'oauth_token_secret' => 'access_token_secret')
        ));
        $this->assertEqual('fake_twitter', $this->Controller->Twitter->settings['datasource']);
        $this->assertEqual('access_token', $this->Controller->Twitter->settings['fields']['oauth_token']);
        $this->assertEqual('access_token_secret', $this->Controller->Twitter->settings['fields']['oauth_token_secret']);
    }

    function testInitialize_with_AuthComponent() {
        $this->Controller->Auth = new Object();
        $this->Controller->Twitter->initialize($this->Controller);
        $this->assertIdentical(array('plugin' => 'twim', 'controller' => 'oauth', 'action' => 'login'), $this->Controller->Auth->loginAction);
    }

    // =========================================================================
    function testGetTwimSource() {
        $this->assertIsA($this->Controller->Twitter->getTwimSource(), 'TwimSource');
        $this->assertEqual('test_twitter_component', $this->Controller->Twitter->getTwimSource()->configKeyName);
    }

    // =========================================================================
    function testGetAuthorizedUrl() {
        $callback = Router::url('/twim/oauth/callback', true);
        $result = $this->Controller->Twitter->getAuthorizeUrl($callback);
        $this->assertPattern('!https://api\.twitter\.com/oauth/authorize\?oauth_token=.+!', $result);
    }

    // =========================================================================
    function testGetAuthenticateUrl() {
        $callback = Router::url('/twim/oauth/callback', true);
        $result = $this->Controller->Twitter->getAuthenticateUrl($callback);
        $this->assertPattern('!https://api\.twitter\.com/oauth/authenticate\?oauth_token=.+!', $result);

        $this->authenticateUrl = $result;
    }

    // =========================================================================
    function testGetAccessToken_noToken() {
        $result = $this->Controller->Twitter->getAccessToken();
        $this->assertFalse($result);
    }

    function testGetAccessToken() {

        $result = array();
        $this->Controller->params['url']['oauth_token'] = 'vkwlQH1uLWWahUNa7PNE6RbBTYGotugP9wh3NSoT0';
        $this->Controller->params['url']['oauth_verifier'] = 'DUWU7DpwCGYNgKbq1B9Pf3uhwVDLyv9XvTP3T3DVAo';
        try {
            $result = $this->Controller->Twitter->getAccessToken();
        } catch (RuntimeException $e) {
            $this->assertTrue('Invalid / expired Token', $e->getMessage());
            return;
        }

        $this->assertIsA($result['oauth_token'], 'String');
        $this->assertIsA($result['oauth_token_secret'], 'String');
        $this->assertIsA($result['user_id'], 'String');
        $this->assertIsA($result['screen_name'], 'String');
    }

    // =========================================================================
    function testSetToken_null_params() {
        $result = $this->Controller->Twitter->setToken('');
        $this->assertEqual('', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
        $this->assertEqual('', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
    }

    function testSetToken_with_array() {
        $result = $this->Controller->Twitter->setToken(array('oauth_token' => 'dummy_token', 'oauth_token_secret' => 'dummy_secret'));
        $this->assertEqual('dummy_token', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
        $this->assertEqual('dummy_secret', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
    }

    function testSetToken() {
        $result = $this->Controller->Twitter->setToken('dummy_token2', 'dummy_secret2');
        $this->assertEqual('dummy_token2', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
        $this->assertEqual('dummy_secret2', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
    }

    function testSetTokenByUser() {
        $user = array(
            'User' => array(
                'oauth_token' => 'dummy_token',
                'oauth_token_secret' => 'dummy_secret',
                ));
        $result = $this->Controller->Twitter->setTokenByUser($user);
        $this->assertEqual('dummy_token', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token']);
        $this->assertEqual('dummy_secret', $this->Controller->Twitter->TwimOauth->getDataSource()->config['oauth_token_secret']);
    }

    function testSetTokenByUser_change_field_name() {
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

}
