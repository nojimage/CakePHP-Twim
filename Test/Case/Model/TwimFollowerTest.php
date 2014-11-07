<?php

/**
 * test TwimFollower
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
App::uses('TwimFollower', 'Twim.Model');

/**
 *
 * @property TwimFollower $Follower
 */
class TwimFollowerTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Follower = ClassRegistry::init('Twim.TwimFollower');
		$this->Follower->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->Follower);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testIds_using_user_id() {
		$this->Follower->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Follower->find(TwimFollower::FINDTYPE_IDS, array('user_id' => '1234'));
		$this->assertSame('1.1/followers/ids', $this->Follower->request['uri']['path']);
		$this->assertSame(array('user_id' => '1234'), $this->Follower->request['uri']['query']);
	}

	public function testIds_using_screen_name() {
		$this->Follower->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Follower->find('ids', array('screen_name' => 'foo'));
		$this->assertSame('1.1/followers/ids', $this->Follower->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'foo'), $this->Follower->request['uri']['query']);
	}

	// =========================================================================

	public function testList_using_user_id() {
		$this->Follower->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Follower->find(TwimFollower::FINDTYPE_LIST, array('user_id' => '783214'));
		$this->assertSame('1.1/followers/list', $this->Follower->request['uri']['path']);
		$this->assertSame(array('user_id' => '783214'), $this->Follower->request['uri']['query']);
	}

	public function testList_using_screen_name() {
		$this->Follower->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Follower->find('list', array('screen_name' => 'twitterapi'));
		$this->assertSame('1.1/followers/list', $this->Follower->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'twitterapi'), $this->Follower->request['uri']['query']);
	}

}
