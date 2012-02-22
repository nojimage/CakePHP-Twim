<?php

/**
 * test Twim model
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
 * @property Twim $Twim
 */
class TwimTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Twim = ClassRegistry::init('Twim.Twim');
	}

	public function tearDown() {
		unset($this->Twim);
		parent::tearDown();
	}

	public function testSerach() {
		$q = 'test';
		$limit = 100;
		$results = $this->Twim->Search->find('search', compact('q', 'limit'));
		$this->assertNotEmpty($results);
		$this->assertCount(100, $results);
	}

}
