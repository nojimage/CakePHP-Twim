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
App::import('Model', 'Twim.TwimAccount');
App::import('Datasource', array('Twim.TwimSource'));

class TestTwimAccount extends TwimAccount {

    public $alias = 'TwimAccount';
    public $useDbConfig = 'test_twitter_account';

}

Mock::generatePartial('TwimSource', 'MockTwimAccountTwimSource', array('request'));

/**
 *
 * @property TwimAccount $Account
 */
class TwimAccountTestCase extends CakeTestCase {

    public function startCase() {
        ConnectionManager::create('test_twitter_account', array('datasource' => 'MockTwimAccountTwimSource'));
    }

    public function startTest() {
        $this->Account = ClassRegistry::init('Twim.TestTwimAccount');
    }

    public function endTest() {
        unset($this->Account);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function testRateLimitStatus() {
        $this->Account->setDataSource('twitter');
        $limit = $this->Account->find('rateLimitStatus');
        $this->assertTrue(isset($limit['hourly_limit']));
        $this->assertTrue(isset($limit['reset_time_in_seconds']));
        $this->assertTrue(isset($limit['reset_time']));
        $this->assertTrue(isset($limit['remaining_hits']));
    }

}
