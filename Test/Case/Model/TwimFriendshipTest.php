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
		ob_flush();
	}

	// =========================================================================

	public function testExists_call_find_method_using_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('exists', array('user_id_a' => '1234', 'user_id_b' => '5678'));
		$this->assertSame('1/friendships/exists', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('user_id_a' => '1234', 'user_id_b' => '5678'), $this->Friendship->request['uri']['query']);
	}

	public function testExists_call_find_method_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('exists', array('screen_name_a' => 'foo', 'screen_name_b' => 'bar'));
		$this->assertSame('1/friendships/exists', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('screen_name_a' => 'foo', 'screen_name_b' => 'bar'), $this->Friendship->request['uri']['query']);
	}

	public function testExists_call_exists_method_using_one_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->set('id', '1234');
		$this->Friendship->exists('5678');
		$this->assertSame('1/friendships/exists', $this->Friendship->exists_request['uri']['path']);
		$this->assertSame(array('user_id_a' => '1234', 'user_id_b' => '5678'), $this->Friendship->exists_request['uri']['query']);
	}

	public function testExists_call_exists_method_using_two_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->exists('1234', '5678');
		$this->assertSame('1/friendships/exists', $this->Friendship->exists_request['uri']['path']);
		$this->assertSame(array('user_id_a' => '1234', 'user_id_b' => '5678'), $this->Friendship->exists_request['uri']['query']);
	}

	public function testExists_call_exists_method_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->exists('foo', 'bar');
		$this->assertSame('1/friendships/exists', $this->Friendship->exists_request['uri']['path']);
		$this->assertSame(array('screen_name_a' => 'foo', 'screen_name_b' => 'bar'), $this->Friendship->exists_request['uri']['query']);
	}

	public function testExists_real() {
		$this->Friendship->setDataSource($this->testDatasourceName);
		$this->assertFalse($this->Friendship->exists('cakephp', 'nojimage'));
		$this->assertTrue($this->Friendship->exists('nojimage', 'cakephp'));
	}

	// =========================================================================

	public function testIncoming() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('incoming', array('stringify_ids' => true));
		$this->assertSame('1/friendships/incoming', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('stringify_ids' => true), $this->Friendship->request['uri']['query']);
	}

	// =========================================================================

	public function testOutgoing() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('outgoing', array('stringify_ids' => true));
		$this->assertSame('1/friendships/outgoing', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('stringify_ids' => true), $this->Friendship->request['uri']['query']);
	}

	// =========================================================================

	public function testShow_using_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('show', array('source_id' => '1234', 'target_id' => '5678'));
		$this->assertSame('1/friendships/show', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('source_id' => '1234', 'target_id' => '5678'), $this->Friendship->request['uri']['query']);
	}

	public function testShow_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('show', array('source_screen_name' => 'foo', 'target_screen_name' => 'bar'));
		$this->assertSame('1/friendships/show', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('source_screen_name' => 'foo', 'target_screen_name' => 'bar'), $this->Friendship->request['uri']['query']);
	}

	public function testShow_real() {
		$this->Friendship->setDataSource($this->testDatasourceName);
		$result = $this->Friendship->find('show', array('source_screen_name' => 'cakephp', 'target_screen_name' => 'nojimage'));
		$this->assertSame('cakephp', $result['relationship']['source']['screen_name']);
		$this->assertFalse($result['relationship']['source']['following']);
		$this->assertTrue($result['relationship']['source']['followed_by']);
		$this->assertSame('nojimage', $result['relationship']['target']['screen_name']);
		$this->assertTrue($result['relationship']['target']['following']);
		$this->assertFalse($result['relationship']['target']['followed_by']);
	}

	// =========================================================================

	public function testLookup_using_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('lookup', array('user_id' => '783214,6253282'));
		$this->assertSame('1/friendships/lookup', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('user_id' => '783214,6253282'), $this->Friendship->request['uri']['query']);
	}

	public function testLookupw_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('lookup', array('screen_name' => 'twitterapi,twitter'));
		$this->assertSame('1/friendships/lookup', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'twitterapi,twitter'), $this->Friendship->request['uri']['query']);
	}

	// =========================================================================

	public function testNoRetweetIds() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('noRetweetIds', array('stringify_ids' => true));
		$this->assertSame('1/friendships/no_retweet_ids', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('stringify_ids' => true), $this->Friendship->request['uri']['query']);
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
		$this->assertSame('1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('user_id' => '1234'), $this->Friendship->request['body']);
	}

	public function testCreate_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = array(
			'TwimFriendship' => array(
				'screen_name' => 'foo',
			),
		);
		$this->Friendship->create($data);
		$this->assertSame('1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Friendship->request['body']);
	}

	public function testCreate_string_param() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = '1234';
		$this->Friendship->create($data);
		$this->assertSame('1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('user_id' => '1234'), $this->Friendship->request['body']);
	}

	public function testCreate_string_param_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = 'foo';
		$this->Friendship->create($data);
		$this->assertSame('1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Friendship->request['body']);
	}

	public function testCreate_simple_array() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = array('screen_name' => 'foo');
		$this->Friendship->create($data);
		$this->assertSame('1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Friendship->request['body']);
	}

	// =========================================================================

	public function testDelete_by_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->delete('1234');
		$this->assertSame('1/friendships/destroy', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('user_id' => '1234'), $this->Friendship->request['body']);
	}

	public function testDelete_by_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->delete('foo');
		$this->assertSame('1/friendships/destroy', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Friendship->request['body']);
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
		$this->assertSame('1/friendships/update', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('user_id' => '1234', 'device' => false, 'retweets' => false), $this->Friendship->request['body']);
	}

}
