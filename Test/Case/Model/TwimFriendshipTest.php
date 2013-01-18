<?php

/**
 * test TwimFriendship
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
App::uses('TwimFriendship', 'Twim.Model');

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

	public function testExists_using_one_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->set('id', '1234');
		$this->Friendship->exists('5678');
		$this->assertSame('1.1/friendships/show', $this->Friendship->exists_request['uri']['path']);
		$this->assertSame(array('source_id' => '1234', 'target_id' => '5678'), $this->Friendship->exists_request['uri']['query']);
	}

	public function testExists_using_two_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->exists('1234', '5678');
		$this->assertSame('1.1/friendships/show', $this->Friendship->exists_request['uri']['path']);
		$this->assertSame(array('source_id' => '1234', 'target_id' => '5678'), $this->Friendship->exists_request['uri']['query']);
	}

	public function testExists_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->exists('foo', 'bar');
		$this->assertSame('1.1/friendships/show', $this->Friendship->exists_request['uri']['path']);
		$this->assertSame(array('source_screen_name' => 'foo', 'target_screen_name' => 'bar'), $this->Friendship->exists_request['uri']['query']);
	}

	// =========================================================================

	public function testIncoming() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find(TwimFriendship::FINDTYPE_INCOMING, array('stringify_ids' => true));
		$this->assertSame('1.1/friendships/incoming', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('stringify_ids' => true), $this->Friendship->request['uri']['query']);
	}

	// =========================================================================

	public function testOutgoing() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find(TwimFriendship::FINDTYPE_OUTGOING, array('stringify_ids' => true));
		$this->assertSame('1.1/friendships/outgoing', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('stringify_ids' => true), $this->Friendship->request['uri']['query']);
	}

	// =========================================================================

	public function testShow_using_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find(TwimFriendship::FINDTYPE_SHOW, array('source_id' => '1234', 'target_id' => '5678'));
		$this->assertSame('1.1/friendships/show', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('source_id' => '1234', 'target_id' => '5678'), $this->Friendship->request['uri']['query']);
	}

	public function testShow_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('show', array('source_screen_name' => 'foo', 'target_screen_name' => 'bar'));
		$this->assertSame('1.1/friendships/show', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('source_screen_name' => 'foo', 'target_screen_name' => 'bar'), $this->Friendship->request['uri']['query']);
	}

	// =========================================================================

	public function testLookup_using_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find(TwimFriendship::FINDTYPE_LOOKUP, array('user_id' => '783214,6253282'));
		$this->assertSame('1.1/friendships/lookup', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('user_id' => '783214,6253282'), $this->Friendship->request['uri']['query']);
	}

	public function testLookupw_using_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friendship->find('lookup', array('screen_name' => 'twitterapi,twitter'));
		$this->assertSame('1.1/friendships/lookup', $this->Friendship->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'twitterapi,twitter'), $this->Friendship->request['uri']['query']);
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
		$this->assertSame('1.1/friendships/create', $this->Friendship->request['uri']['path']);
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
		$this->assertSame('1.1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Friendship->request['body']);
	}

	public function testCreate_string_param() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = '1234';
		$this->Friendship->create($data);
		$this->assertSame('1.1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('user_id' => '1234'), $this->Friendship->request['body']);
	}

	public function testCreate_string_param_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = 'foo';
		$this->Friendship->create($data);
		$this->assertSame('1.1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Friendship->request['body']);
	}

	public function testCreate_simple_array() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$data = array('screen_name' => 'foo');
		$this->Friendship->create($data);
		$this->assertSame('1.1/friendships/create', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Friendship->request['body']);
	}

	// =========================================================================

	public function testDelete_by_user_id() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->delete('1234');
		$this->assertSame('1.1/friendships/destroy', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('user_id' => '1234'), $this->Friendship->request['body']);
	}

	public function testDelete_by_screen_name() {
		$this->Friendship->getDataSource()->expects($this->once())->method('request');
		$this->Friendship->delete('foo');
		$this->assertSame('1.1/friendships/destroy', $this->Friendship->request['uri']['path']);
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
		$this->assertSame('1.1/friendships/update', $this->Friendship->request['uri']['path']);
		$this->assertSame('POST', $this->Friendship->request['method']);
		$this->assertSame(array('user_id' => '1234', 'device' => false, 'retweets' => false), $this->Friendship->request['body']);
	}

}
