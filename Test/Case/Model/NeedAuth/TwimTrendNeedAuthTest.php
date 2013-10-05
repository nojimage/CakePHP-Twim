<?php

/**
 * test TwimTrend
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
 * @property TwimTrend $Trend
 */
class TwimTrendNeedAuthTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->Trend = ClassRegistry::init('Twim.TwimTrend');
		$this->Trend->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->Trend);
		parent::tearDown();
	}

	// =========================================================================

	public function testPlace() {
		$result = $this->Trend->find(TwimTrend::FINDTYPE_PLACE, array('id' => 1));

		$this->assertSame(1, $result[0]['locations'][0]['woeid']);
		$this->assertCount(10, $result[0]['trends']);
		$this->assertArrayHasKey('promoted_content', $result[0]['trends'][0]);
		$this->assertArrayHasKey('events', $result[0]['trends'][0]);
		$this->assertArrayHasKey('url', $result[0]['trends'][0]);
		$this->assertArrayHasKey('name', $result[0]['trends'][0]);
		$this->assertArrayHasKey('query', $result[0]['trends'][0]);
	}

	// =========================================================================

	public function testAvailable() {
		$result = $this->Trend->find(TwimTrend::FINDTYPE_AVAILABLE);

		$this->assertArrayHasKey('url', $result[0]);
		$this->assertArrayHasKey('placeType', $result[0]);
		$this->assertArrayHasKey('countryCode', $result[0]);
		$this->assertArrayHasKey('name', $result[0]);
		$this->assertArrayHasKey('parentid', $result[0]);
		$this->assertArrayHasKey('woeid', $result[0]);
		$this->assertArrayHasKey('country', $result[0]);
	}

	// =========================================================================

	public function testClosest() {
		$result = $this->Trend->find(TwimTrend::FINDTYPE_CLOSEST, array('lat' => 37.781157, 'long' => -122.400612831116));

		$this->assertCount(1, $result);
		$this->assertArrayHasKey('url', $result[0]);
		$this->assertArrayHasKey('placeType', $result[0]);
		$this->assertArrayHasKey('countryCode', $result[0]);
		$this->assertArrayHasKey('name', $result[0]);
		$this->assertArrayHasKey('parentid', $result[0]);
		$this->assertArrayHasKey('woeid', $result[0]);
		$this->assertArrayHasKey('country', $result[0]);
	}

}
