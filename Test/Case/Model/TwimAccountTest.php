<?php

/**
 * test TwimAccount
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
 * @property TwimAccount $Account
 */
class TwimAccountTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Account = ClassRegistry::init('Twim.TwimAccount');
		$this->Account->setDataSource($this->testDatasourceName);
	}

	public function tearDown() {
		unset($this->Account);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================


	public function testRateLimitStatus() {
		$limit = $this->Account->find('rateLimitStatus');
		$this->assertInternalType('integer', $limit['hourly_limit']);
		$this->assertInternalType('integer', $limit['remaining_hits']);
		$this->assertGreaterThan(time(), $limit['reset_time_in_seconds']);
		$this->assertNotEmpty($limit['reset_time']);
	}

	// =========================================================================

	public function testGetApiRemain() {
		$this->assertGreaterThanOrEqual(0, $this->Account->getApiRemain());
	}

	// =========================================================================

	public function testGetApiResetTime() {
		$this->assertGreaterThan(time(), $this->Account->getApiResetTime());
	}

}
