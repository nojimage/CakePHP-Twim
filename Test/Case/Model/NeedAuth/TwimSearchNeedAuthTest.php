<?php

/**
 * test TwimSearch
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
 * @property TwimSearch $Search
 */
class TwimSearchNeedAuthTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->Search = ClassRegistry::init('Twim.TwimSearch');
		$this->Search->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->Search);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

/**
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage You must enter a query.
 */
	public function testSerach_noquery() {
		$this->Search->find('');
	}

	public function testSerach_get_all_results() {
		$results = $this->Search->find('test');
		$this->assertGreaterThan(100, count($results));
	}

/**
 * @depends testSerach_get_all_results
 */
	public function testSerach_limitation_results() {
		$this->assertSame(255, count($this->Search->find('search', array('q' => 'test', 'limit' => 255))));
	}

}
