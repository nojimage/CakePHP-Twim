<?php

/**
 * Twitter Authenticatable Behavior Test Case
 */
App::import('Core', array('AppModel', 'Model'));
App::import('Behavior', array('Twim.TwitterAuth'));

Mock::generate('AppModel', 'MockModel');

class TwitterAuthTestModel extends AppModel {

    public $name = 'TwitterUser';
    public $alias = 'TwitterUser';
    public $useTable = false;
    public $actsAs = array(
        'Twim.TwitterAuth',
    );
    public $_schema = array(
        'id' => true,
        'username' => true,
        'password' => true,
        'oauth_token' => true,
        'oauth_token_secret' => true,
    );

}

/**
 * @property TwitterAuthenticatableTestModel $Model
 */
class TwitterAuthBehaviorTest extends CakeTestCase {

    public $fixtures = array();

    /**
     * startTest method
     *
     * @access public
     * @return void
     */
    public function startTest($method) {
        $this->Model = ClassRegistry::init('TwitterAuthTestModel');
    }

    /**
     * endTest method
     *
     * @access public
     * @return void
     */
    public function endTest($method) {
        unset($this->Model);
        ClassRegistry::flush();
    }

    public function testCreateSaveDataByToken() {
        $data = array(
            'user_id' => '123456789',
            'screen_name' => 'dummy_user',
            'oauth_token' => 'dummy token',
            'oauth_token_secret' => 'dummy secret token',
        );
        $ok = array(
            'TwitterUser' => array(
                'id' => '123456789',
                'username' => 'dummy_user',
                'oauth_token' => 'dummy token',
                'oauth_token_secret' => 'dummy secret token',
                'password' => 'ae9277742549f954cb43408b44fd3610a5b5e9db',
            ),
        );
        $this->assertIdentical($ok, $this->Model->createSaveDataByToken($data));
    }

}