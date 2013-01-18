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
class TwimFriendshipNeetAuthTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->Friendship = ClassRegistry::init('Twim.TwimFriendship');
		$this->Friendship->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->Friendship);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testExists() {
		$this->assertFalse($this->Friendship->exists('cakephp', 'nojimage'));
		$this->assertTrue($this->Friendship->exists('nojimage', 'cakephp'));
	}

	public function testShow() {
		$result = $this->Friendship->find('show', array('source_screen_name' => 'cakephp', 'target_screen_name' => 'nojimage'));
		$this->assertSame('cakephp', $result['relationship']['source']['screen_name']);
		$this->assertFalse($result['relationship']['source']['following']);
		$this->assertTrue($result['relationship']['source']['followed_by']);
		$this->assertSame('nojimage', $result['relationship']['target']['screen_name']);
		$this->assertTrue($result['relationship']['target']['following']);
		$this->assertFalse($result['relationship']['target']['followed_by']);
	}

}
