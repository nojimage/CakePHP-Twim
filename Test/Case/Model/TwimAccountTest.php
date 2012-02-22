<?php

/**
 * test TwimAccount
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
 * @link    　http://php-tips.com/
 * @since   　File available since Release 1.0
 *
 */
App::import('Lib', 'Twim.TwimConnectionTestCase');
App::import('Model', 'Twim.TwimAccount');

/**
 *
 * @property TwimAccount $Account
 */
class TwimAccountTestCase extends TwimConnectionTestCase {

    public function startTest() {
        $this->Account = ClassRegistry::init('Twim.TwimAccount');
        $this->Account->setDataSource($this->testDatasourceName);
    }

    public function endTest() {
        unset($this->Account);
        ClassRegistry::flush();
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
