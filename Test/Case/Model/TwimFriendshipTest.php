<?php

/**
 * test TwimFriendship
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
 *
 */
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 *
 * @property TwimFriendship $Friendship
 */
class TwimFriendshipTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Friendship = ClassRegistry::init('Twim.TwimFriendship');
		$this->Friendship->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->Friendship);
		parent::tearDown();
	}

	// =========================================================================

	public function testExists_call_find_method_using_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('exists', array('user_id_a' => '1234', 'user_id_b' => '5678'));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/exists');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('user_id_a' => '1234', 'user_id_b' => '5678'));
	}

	public function testExists_call_find_method_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('exists', array('screen_name_a' => 'foo', 'screen_name_b' => 'bar'));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/exists');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('screen_name_a' => 'foo', 'screen_name_b' => 'bar'));
	}

	public function testExists_call_exists_method_using_one_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->set('id', '1234');
		$this->Friendship->exists('5678');
		$this->assertIdentical($this->Friendship->exists_request['uri']['path'], '1/friendships/exists');
		$this->assertIdentical($this->Friendship->exists_request['uri']['query'], array('user_id_a' => '1234', 'user_id_b' => '5678'));
	}

	public function testExists_call_exists_method_using_two_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->exists('1234', '5678');
		$this->assertIdentical($this->Friendship->exists_request['uri']['path'], '1/friendships/exists');
		$this->assertIdentical($this->Friendship->exists_request['uri']['query'], array('user_id_a' => '1234', 'user_id_b' => '5678'));
	}

	public function testExists_call_exists_method_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->exists('foo', 'bar');
		$this->assertIdentical($this->Friendship->exists_request['uri']['path'], '1/friendships/exists');
		$this->assertIdentical($this->Friendship->exists_request['uri']['query'], array('screen_name_a' => 'foo', 'screen_name_b' => 'bar'));
	}

	public function testExists_real() {
		$this->Friendship->setDataSource($this->testDatasourceName);
		$this->assertFalse($this->Friendship->exists('cakephp', 'nojimage'));
		$this->assertTrue($this->Friendship->exists('nojimage', 'cakephp'));
	}

	// =========================================================================

	public function testIncoming() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('incoming', array('stringify_ids' => true));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/incoming');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('stringify_ids' => true));
	}

	// =========================================================================

	public function testOutgoing() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('outgoing', array('stringify_ids' => true));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/outgoing');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('stringify_ids' => true));
	}

	// =========================================================================

	public function testShow_using_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('show', array('source_id' => '1234', 'target_id' => '5678'));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/show');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('source_id' => '1234', 'target_id' => '5678'));
	}

	public function testShow_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('show', array('source_screen_name' => 'foo', 'target_screen_name' => 'bar'));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/show');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('source_screen_name' => 'foo', 'target_screen_name' => 'bar'));
	}

	public function testShow_real() {
		$this->Friendship->setDataSource($this->testDatasourceName);
		$result = $this->Friendship->find('show', array('source_screen_name' => 'cakephp', 'target_screen_name' => 'nojimage'));
		$this->assertEqual('cakephp', $result['relationship']['source']['screen_name']);
		$this->assertFalse($result['relationship']['source']['following']);
		$this->assertTrue($result['relationship']['source']['followed_by']);
		$this->assertEqual('nojimage', $result['relationship']['target']['screen_name']);
		$this->assertTrue($result['relationship']['target']['following']);
		$this->assertFalse($result['relationship']['target']['followed_by']);
	}

	// =========================================================================

	public function testLookup_using_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('lookup', array('user_id' => '783214,6253282'));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/lookup');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('user_id' => '783214,6253282'));
	}

	public function testLookupw_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('lookup', array('screen_name' => 'twitterapi,twitter'));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/lookup');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('screen_name' => 'twitterapi,twitter'));
	}

	// =========================================================================

	public function testNoRetweetIds() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->find('noRetweetIds', array('stringify_ids' => true));
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/no_retweet_ids');
		$this->assertIdentical($this->Friendship->request['uri']['query'], array('stringify_ids' => true));
	}

	// =========================================================================

	public function testCreate() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = array(
			'TwimFriendship' => array(
				'user_id' => '1234',
			),
		);
		$this->Friendship->create($data);
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/create');
		$this->assertIdentical($this->Friendship->request['method'], 'POST');
		$this->assertIdentical($this->Friendship->request['body'], array('user_id' => '1234'));
	}

	public function testCreate_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = array(
			'TwimFriendship' => array(
				'screen_name' => 'foo',
			),
		);
		$this->Friendship->create($data);
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/create');
		$this->assertIdentical($this->Friendship->request['method'], 'POST');
		$this->assertIdentical($this->Friendship->request['body'], array('screen_name' => 'foo'));
	}

	public function testCreate_string_param() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = '1234';
		$this->Friendship->create($data);
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/create');
		$this->assertIdentical($this->Friendship->request['method'], 'POST');
		$this->assertIdentical($this->Friendship->request['body'], array('user_id' => '1234'));
	}

	public function testCreate_string_param_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = 'foo';
		$this->Friendship->create($data);
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/create');
		$this->assertIdentical($this->Friendship->request['method'], 'POST');
		$this->assertIdentical($this->Friendship->request['body'], array('screen_name' => 'foo'));
	}

	public function testCreate_simple_array() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = array('screen_name' => 'foo');
		$this->Friendship->create($data);
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/create');
		$this->assertIdentical($this->Friendship->request['method'], 'POST');
		$this->assertIdentical($this->Friendship->request['body'], array('screen_name' => 'foo'));
	}

	// =========================================================================

	public function testDelete_by_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->delete('1234');
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/destroy');
		$this->assertIdentical($this->Friendship->request['method'], 'POST');
		$this->assertIdentical($this->Friendship->request['body'], array('user_id' => '1234'));
	}

	public function testDelete_by_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->delete('foo');
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/destroy');
		$this->assertIdentical($this->Friendship->request['method'], 'POST');
		$this->assertIdentical($this->Friendship->request['body'], array('screen_name' => 'foo'));
	}

	// =========================================================================

	public function testUpdate() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = array(
			'TwimFriendship' => array(
				'user_id' => '1234',
				'device' => false,
				'retweets' => false,
			),
		);
		$this->Friendship->update($data);
		$this->assertIdentical($this->Friendship->request['uri']['path'], '1/friendships/update');
		$this->assertIdentical($this->Friendship->request['method'], 'POST');
		$this->assertIdentical($this->Friendship->request['body'], array('user_id' => '1234', 'device' => false, 'retweets' => false));
	}

}
