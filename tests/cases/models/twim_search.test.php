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
App::import('Datasource', array('Twim.TwimSource'));

class TestTwimSearch extends TwimSearch {

    public $alias = 'TwimSearch';
    public $useDbConfig = 'test_twitter_search';

}

Mock::generatePartial('TwimSource', 'MockTwimSearchTwimSource', array('request'));

/**
 *
 * @property TwimSearch $Search
 */
class TwimSearchTestCase extends CakeTestCase {

    public function startCase() {
        ConnectionManager::create('test_twitter_search', array('datasource' => 'MockTwimSearchTwimSource'));
    }

    public function startTest() {
        $this->Search = ClassRegistry::init('Twim.TestTwimSearch');
    }

    public function endTest() {
        unset($this->Search);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function testSerach() {
        $q = 'test';
        $page = 1;
        $limit = 50;
        $this->Search->getDataSource()->expectOnce('request');

        $this->Search->find('search', compact('q', 'limit', 'page'));

        $this->assertIdentical($this->Search->request['uri']['host'], 'search.twitter.com');
        $this->assertIdentical($this->Search->request['uri']['path'], 'search');
        $this->assertEqual($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 50));
    }

    public function testSerach_call2() {

        $this->Search->getDataSource()->expectOnce('request');
        $this->Search->find('search', 'test');
        $this->assertEqual($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 100));
    }

    public function testSerach_call3() {

        $this->Search->getDataSource()->expectOnce('request');
        $this->Search->find('test');
        $this->assertEqual($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 100));
    }

    public function testSerach_noquery() {

        $this->Search->setDataSource('twitter');
        try {
            $this->Search->find('');
        } catch (RuntimeException $e) {
            $this->assertIdentical($e->getMessage(), 'You must enter a query.');
        }
        $this->assertEqual($this->Search->request['uri']['query'], array('q' => '', 'page' => 1, 'rpp' => 100));
    }

    public function testSerach_get_all_results() {
        $this->Search->setDataSource('twitter');
        $this->assertTrue(count($this->Search->find('twitter')) > 100);
        $this->assertTrue(empty($this->Search->response['next_page']));
    }

    public function testSerach_limitation_results() {
        $this->Search->setDataSource('twitter');
        $this->assertIdentical(255, count($this->Search->find('search', array('q' => 'twitter', 'limit' => 255))));
        $this->assertFalse(empty($this->Search->response['next_page']));
    }

    public function test_serach_get_empty_results() {
        $this->Search->getDataSource()->expectOnce('request');
        $this->Search->getDataSource()->setReturnValue('request', array('results' => array()));
        $result = $this->Search->find('nwoghwiot20gflanvowigiwoagnla;424ty9agfjpoafacdj4#eqpwkp');
        $this->assertIdentical(array(), $result);
    }

}
