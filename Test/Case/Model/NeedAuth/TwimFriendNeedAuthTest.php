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
class TwimFriendNeetAuthTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->Friend = ClassRegistry::init('Twim.TwimFriend');
		$this->Friend->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->Friend);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testIds() {
		$result = $this->Friend->find('ids', array(
			'screen_name' => 'cakephp',
			'count' => 10,
		));
		$this->assertCount(10, $result['ids']);
		$this->assertArrayHasKey('next_cursor', $result);
		$this->assertArrayHasKey('previous_cursor', $result);
	}

	public function testList() {
		$result = $this->Friend->find('list', array(
			'screen_name' => 'cakephp',
			'skip_status' => true,
			'count' => 5,
		));
		$this->assertCount(5, $result['users']);
		$this->assertArrayHasKey('id', $result['users'][0]);
		$this->assertArrayHasKey('screen_name', $result['users'][0]);
		$this->assertArrayHasKey('name', $result['users'][0]);
		$this->assertArrayHasKey('followers_count', $result['users'][0]);
		$this->assertArrayHasKey('next_cursor', $result);
		$this->assertArrayHasKey('previous_cursor', $result);
	}

}
