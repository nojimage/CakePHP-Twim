<?php

/**
 * test TwimFriend
 *
 * CakePHP 2.x
 * PHP version 5
 *
 * Copyright 2014, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   2.1
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2014 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   Twim
 * @since     File available since Release 1.0
 *
 */
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');
App::uses('TwimFriend', 'Twim.Model');

/**
 *
 * @property TwimFriend $Friend
 */
class TwimFriendTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Friend = ClassRegistry::init('Twim.TwimFriend');
		$this->Friend->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->Friend);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testIds_using_user_id() {
		$this->Friend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friend->find(TwimFriend::FINDTYPE_IDS, array('user_id' => '1234'));
		$this->assertSame('1.1/friends/ids', $this->Friend->request['uri']['path']);
		$this->assertSame(array('user_id' => '1234'), $this->Friend->request['uri']['query']);
	}

	public function testIds_using_screen_name() {
		$this->Friend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friend->find('ids', array('screen_name' => 'foo'));
		$this->assertSame('1.1/friends/ids', $this->Friend->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Friend->request['uri']['query']);
	}

	// =========================================================================

	public function testList_using_user_id() {
		$this->Friend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friend->find(TwimFriend::FINDTYPE_LIST, array('user_id' => '783214'));
		$this->assertSame('1.1/friends/list', $this->Friend->request['uri']['path']);
		$this->assertSame(array('user_id' => '783214'), $this->Friend->request['uri']['query']);
	}

	public function testList_using_screen_name() {
		$this->Friend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Friend->find('list', array('screen_name' => 'twitterapi'));
		$this->assertSame('1.1/friends/list', $this->Friend->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'twitterapi'), $this->Friend->request['uri']['query']);
	}

}
