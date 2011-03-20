<?php

/**
 * test TwimSearch
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
App::import('Model', 'Twim.TwimSearch');
App::import('Datasource', array('Twitter.TwitterSource'));

class TestTwimSearch extends TwimSearch {

    public $alias = 'TwimSearch';
    public $useDbConfig = 'test_twitter';

}

Mock::generatePartial('TwitterSource', 'MockTwimSearchTwitterSource', array('request'));

/**
 *
 * @property TwimSearch $Search
 */
class TwimSearchTestCase extends CakeTestCase {

    public function startTest() {
        ConnectionManager::create('test_twitter',
                        array('datasource' => 'MockTwimSearchTwitterSource'));

        $this->Search = ClassRegistry::init('Twim.TestTwimSearch');
    }

    public function endTest() {
        unset($this->Search);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function test_serach() {
        $q = 'test';
        $page = 1;
        $limit = 50;
        $this->Search->getDataSource()->expectOnce('request');

        $this->Search->find('search', compact('q', 'limit', 'page'));

        $this->assertIdentical($this->Search->request['uri']['host'], 'search.twitter.com');
        $this->assertIdentical($this->Search->request['uri']['path'], 'search');
        $this->assertIdentical($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 50));
    }

    public function test_serach_call2() {

        $this->Search->getDataSource()->expectOnce('request');
        $this->Search->find('search', 'test');
        $this->assertIdentical($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 200));
    }

    public function test_serach_call3() {

        $this->Search->getDataSource()->expectOnce('request');
        $this->Search->find('test');
        $this->assertIdentical($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 200));
    }

    public function test_serach_noquery() {

        $this->Search = new TwimSearch();
        try {
            $this->Search->find('');
        } catch (RuntimeException $e) {
            $this->assertIdentical($e->getMessage(), 'You must enter a query.');
        }
        $this->assertIdentical($this->Search->request['uri']['query'], array('q' => '', 'page' => 1, 'rpp' => 200));
    }

}
