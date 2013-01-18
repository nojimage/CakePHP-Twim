<?php

/**
 * test TwimAccount
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
App::uses('TwimAccount', 'Twim.Model');
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 *
 * @property TwimAccount $Account
 */
class TwimAccountTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->Account = ClassRegistry::init('Twim.TwimAccount');
		$this->Account->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->Account);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testFindSettings() {
		$result = $this->Account->find(TwimAccount::FINDTYPE_SETTINGS);
		$this->assertArrayHasKey('screen_name', $result);
		$this->assertArrayHasKey('language', $result);
	}

	public function testFindVerifyCredentials() {
		$result = $this->Account->find(TwimAccount::FINDTYPE_VERIFY_CREDENTIALS);
		$this->assertArrayHasKey('id', $result);
		$this->assertArrayHasKey('screen_name', $result);
		$this->assertArrayHasKey('lang', $result);
	}

}
