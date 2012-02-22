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
	}

	// =========================================================================


	public function testRateLimitStatus() {
		$limit = $this->Account->find('rateLimitStatus');
		$this->assertTrue(isset($limit['hourly_limit']));
		$this->assertTrue(isset($limit['reset_time_in_seconds']));
		$this->assertTrue(isset($limit['reset_time']));
		$this->assertTrue(isset($limit['remaining_hits']));
	}

	// =========================================================================

	public function testGetApiRemain() {
		$this->assertTrue($this->Account->getApiRemain() > 0);
	}

	// =========================================================================

	public function testGetApiResetTime() {
		$this->assertTrue($this->Account->getApiResetTime() > time());
	}

}
