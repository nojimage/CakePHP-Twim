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

	public function testPlace() {
		$this->Trend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Trend->find(TwimTrend::FINDTYPE_PLACE, array('id' => 1));
		$this->assertSame('1.1/trends/place', $this->Trend->request['uri']['path']);
		$this->assertSame(array('id' => 1), $this->Trend->request['uri']['query']);
	}

	// =========================================================================

	public function testAvailable() {
		$this->Trend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Trend->find(TwimTrend::FINDTYPE_AVAILABLE);
		$this->assertSame('1.1/trends/available', $this->Trend->request['uri']['path']);
		$this->assertSame(array(), $this->Trend->request['uri']['query']);
	}

	// =========================================================================

	public function testClosest() {
		$this->Trend->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Trend->find(TwimTrend::FINDTYPE_CLOSEST);
		$this->assertSame('1.1/trends/closest', $this->Trend->request['uri']['path']);
		$this->assertSame(array(), $this->Trend->request['uri']['query']);
	}

}
