<?php

/**
 * test TwimTrend
 *
 * PHP versions 5
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @version   1.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2011 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   twim
 * @since   　File available since Release 1.0
 *
 */
App::import('Lib', 'Twim.TwimConnectionTestCase');
App::import('Model', 'Twim.TwimTrend');

/**
 *
 * @property TwimTrend $Trend
 */
class TwimTrendTestCase extends TwimConnectionTestCase {

    public function startTest() {
        $this->Trend = ClassRegistry::init('Twim.TwimTrend');
        $this->Trend->setDataSource($this->mockDatasourceName);
    }

    public function endTest() {
        unset($this->Trend);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function test_daily() {
        $this->Trend->getDataSource()->expectOnce('request');
        $this->Trend->find('daily');
        $this->assertIdentical($this->Trend->request['uri']['path'], '1/trends/daily');
        $this->assertIdentical($this->Trend->request['uri']['query'], array());
    }

    // =========================================================================
    public function test_weekly() {
        $this->Trend->getDataSource()->expectOnce('request');
        $this->Trend->find('weekly');
        $this->assertIdentical($this->Trend->request['uri']['path'], '1/trends/weekly');
        $this->assertIdentical($this->Trend->request['uri']['query'], array());
    }

    // =========================================================================
    public function test_available() {
        $this->Trend->getDataSource()->expectOnce('request');
        $this->Trend->find('available');
        $this->assertIdentical($this->Trend->request['uri']['path'], '1/trends/available');
        $this->assertIdentical($this->Trend->request['uri']['query'], array());
    }

    // =========================================================================
    public function test_woeid() {
        $this->Trend->getDataSource()->expectOnce('request');
        $this->Trend->find('woeid', array('woeid' => 1));
        $this->assertIdentical($this->Trend->request['uri']['path'], '1/trends/1');
        $this->assertIdentical($this->Trend->request['uri']['query'], array());
    }

    // =========================================================================
    public function test_daily_real() {
        $this->Trend->setDataSource($this->testDatasourceName);
        $datas = $this->Trend->find('daily');
        $this->assertNotNull($datas['trends']);
        $this->assertNotNull($datas['as_of']);
    }

    // =========================================================================
    public function test_weekly_real() {
        $this->Trend->setDataSource($this->testDatasourceName);
        $datas = $this->Trend->find('weekly');
        $this->assertNotNull($datas['trends']);
        $this->assertNotNull($datas['as_of']);
    }

    // =========================================================================
    public function test_available_real() {
        $this->Trend->setDataSource($this->testDatasourceName);
        $datas = $this->Trend->find('available', array());
        $this->assertNotNull($datas);
    }

    // =========================================================================
    public function test_woeid_real() {
        $this->Trend->setDataSource($this->testDatasourceName);
        $datas = $this->Trend->find('woeid', array('woeid' => 1));
        $this->assertNotNull($datas['trends']);
        $this->assertNotNull($datas['as_of']);
        $this->assertNotNull($datas['locations']);
    }

}
