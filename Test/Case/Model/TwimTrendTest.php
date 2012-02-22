<?php

/**
 * test TwimTrend
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
 * @property TwimTrend $Trend
 */
class TwimTrendTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Trend = ClassRegistry::init('Twim.TwimTrend');
		$this->Trend->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->Trend);
		parent::tearDown();
	}

	// =========================================================================

	public function testDaily() {
		$this->Trend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Trend->find('daily');
		$this->assertIdentical($this->Trend->request['uri']['path'], '1/trends/daily');
		$this->assertIdentical($this->Trend->request['uri']['query'], array());
	}

	// =========================================================================

	public function testWeekly() {
		$this->Trend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Trend->find('weekly');
		$this->assertIdentical($this->Trend->request['uri']['path'], '1/trends/weekly');
		$this->assertIdentical($this->Trend->request['uri']['query'], array());
	}

	// =========================================================================

	public function testAvailable() {
		$this->Trend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Trend->find('available');
		$this->assertIdentical($this->Trend->request['uri']['path'], '1/trends/available');
		$this->assertIdentical($this->Trend->request['uri']['query'], array());
	}

	// =========================================================================

	public function testWoeid() {
		$this->Trend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Trend->find('woeid', array('woeid' => 1));
		$this->assertIdentical($this->Trend->request['uri']['path'], '1/trends/1');
		$this->assertIdentical($this->Trend->request['uri']['query'], array());
	}

	// =========================================================================

	public function testDaily_real() {
		$this->Trend->setDataSource($this->testDatasourceName);
		$datas = $this->Trend->find('daily');
		$this->assertNotNull($datas['trends']);
		$this->assertNotNull($datas['as_of']);
	}

	// =========================================================================

	public function testWeekly_real() {
		$this->Trend->setDataSource($this->testDatasourceName);
		$datas = $this->Trend->find('weekly');
		$this->assertNotNull($datas['trends']);
		$this->assertNotNull($datas['as_of']);
	}

	// =========================================================================

	public function testAvailable_real() {
		$this->Trend->setDataSource($this->testDatasourceName);
		$datas = $this->Trend->find('available', array());
		$this->assertNotNull($datas);
	}

	// =========================================================================

	public function testWoeid_real() {
		$this->Trend->setDataSource($this->testDatasourceName);
		$datas = $this->Trend->find('woeid', array('woeid' => 1));
		$this->assertNotNull($datas['trends']);
		$this->assertNotNull($datas['as_of']);
		$this->assertNotNull($datas['locations']);
	}

}
