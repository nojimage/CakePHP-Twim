<?php

/**
 * test TwimApplication
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
App::uses('TwimApplication', 'Twim.Model');
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 *
 * @property TwimApplication $Application
 */
class TwimApplicationTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->Application = ClassRegistry::init('Twim.TwimApplication');
		$this->Application->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->Application);
		parent::tearDown();
	}

	// =========================================================================

	public function testFindRateLimitStatus() {
		$result = $this->Application->find(TwimApplication::FINDTYPE_RATE_LIMIT_STATUS);
		$this->assertArrayHasKey('rate_limit_context', $result);
		$this->assertArrayHasKey('resources', $result);
	}

	public function testFindRateLimitStatusWithResources() {
		$result = $this->Application->find(TwimApplication::FINDTYPE_RATE_LIMIT_STATUS, array('resources' => 'statuses,users'));
		$this->assertArrayHasKey('statuses', $result['resources']);
		$this->assertArrayHasKey('users', $result['resources']);
		$this->assertArrayNotHasKey('friendship', $result['resources']);
	}

	public function testGetRateLimit() {
		$result = $this->Application->getRateLimit();
		$this->assertGreaterThan(1, $result['/application/rate_limit_status']);
		$this->assertGreaterThan(1, $result['/users/lookup']);
		$this->assertGreaterThan(1, $result['/statuses/home_timeline']);
	}

	public function testGetRateLimitWithGroup() {
		$result = $this->Application->getRateLimit('statuses');
		$this->assertArrayNotHasKey('/application/rate_limit_status', $result);
		$this->assertArrayNotHasKey('/users/lookup', $result);
		$this->assertGreaterThan(1, $result['/statuses/home_timeline']);
	}

	public function testGetRateLimitWithGroup2() {
		$result = $this->Application->getRateLimit('statuses,users');
		$this->assertArrayNotHasKey('/application/rate_limit_status', $result);
		$this->assertGreaterThan(1, $result['/statuses/home_timeline']);
		$this->assertGreaterThan(1, $result['/users/lookup']);
	}

	public function testGetRateLimitWithGroup3() {
		$result = $this->Application->getRateLimit(array('statuses', 'users'));
		$this->assertArrayNotHasKey('/application/rate_limit_status', $result);
		$this->assertGreaterThan(1, $result['/statuses/home_timeline']);
		$this->assertGreaterThan(1, $result['/users/lookup']);
	}

	public function testGetRateLimitWithResource() {
		$result = $this->Application->getRateLimit('statuses/home_timeline');
		$this->assertGreaterThan(1, $result);
	}

}
