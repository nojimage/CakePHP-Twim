<?php

/**
 * test TwimUser (need Auth)
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

/**
 *
 * @property TwimUser $User
 */
class TwimUserNeedAuthTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('Twim.TwimUser');
		$this->User->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->User);
		parent::tearDown();
	}

	// =========================================================================

	public function testLookup_by_screen_name() {
		$results = $this->User->find('lookup', array('screen_name' => array('cakephp', 'nojimage')));
		$results = Set::sort($results, '/id_str', 'asc');
		$screenNames = Set::extract('/screen_name', $results);
		$ids = Set::extract('/id_str', $results);
		$this->assertEquals(array('cakephp', 'nojimage'), $screenNames);
		$this->assertEquals(array('8620662', '15982041'), $ids);
	}

	public function testLookup_by_user_id() {
		$results = $this->User->find('lookup', array('user_id' => array('8620662', '15982041')));
		$results = Set::sort($results, '/id_str', 'asc');
		$screenNames = Set::extract('/screen_name', $results);
		$ids = Set::extract('/id_str', $results);
		$this->assertEquals(array('cakephp', 'nojimage'), $screenNames);
		$this->assertEquals(array('8620662', '15982041'), $ids);
	}

	public function testProfileImage() {
		$result = $this->User->find('profileImage', array('screen_name' => 'twitterapi'));
		$this->assertRegExp('!http://a[0-9]+\.twimg\.com/profile_images/[0-9]+/.+\_normal.png!', $result);
	}

	public function testProfileImageWithSize() {
		$result = $this->User->find('profileImage', array('screen_name' => 'twitterapi', 'size' => 'bigger'));
		$this->assertRegExp('!http://a[0-9]+\.twimg\.com/profile_images/[0-9]+/.+\_bigger.png!', $result);
	}

	public function testSearch() {
		$results = $this->User->find('search', array('q' => 'cake'));
		$this->assertNotEmpty($results[0]['screen_name']);
	}

	public function testShow_by_screen_name() {
		$results = $this->User->find('show', array('screen_name' => 'nojimage'));
		$this->assertEquals('nojimage', $results['screen_name']);
		$this->assertEquals('15982041', $results['id_str']);
	}

	public function testShow_by_user_id() {
		$results = $this->User->find('show', array('user_id' => '8620662'));
		$this->assertEquals('cakephp', $results['screen_name']);
		$this->assertEquals('8620662', $results['id_str']);
	}

	public function testContributees_by_screen_name() {
		$results = $this->User->find('contributees', array('screen_name' => 'themattharris'));
		$this->assertEquals('twitterapi', $results[0]['screen_name']);
	}

	public function testContributees_by_user_id() {
		$results = $this->User->find('contributees', array('user_id' => '819797'));
		$this->assertEquals('twitterapi', $results[0]['screen_name']);
	}

	public function testContributors_by_screen_name() {
		$results = $this->User->find('contributors', array('screen_name' => 'twitter'));
		$this->assertEquals('ryb', $results[0]['screen_name']);
	}

	public function testContributors_by_user_id() {
		$results = $this->User->find('contributors', array('user_id' => '783214'));
		$this->assertEquals('ryb', $results[0]['screen_name']);
	}

}
