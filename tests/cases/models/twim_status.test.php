<?php

/**
 * test TwimStatus
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
App::import('Model', 'Twim.TwimStatus');

/**
 *
 * @property TwimStatus $Status
 */
class TwimStatusTestCase extends TwimConnectionTestCase {

    public function startTest() {
        $this->Status = ClassRegistry::init('Twim.TwimStatus');
        $this->Status->setDataSource($this->mockDatasourceName);
    }

    public function endTest() {
        unset($this->Status);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function test_publicTimeline() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('publicTimeline');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/public_timeline');
        $this->assertIdentical($this->Status->request['uri']['query'], array());
    }

    public function test_publicTimeline_real() {
        $this->Status->setDataSource($this->testDatasourceName);
        $results = $this->Status->find('publicTimeline');
        $this->assertIdentical(count($results), 20);
        $this->assertIdentical(count(Set::extract('/text', $results)), 20);
    }

    // =========================================================================
    public function test_homeTimeline() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('homeTimeline');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/home_timeline');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
    }

    public function test_homeTimeline_with_pageCount() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('homeTimeline', array('page' => 2, 'count' => 100));
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/home_timeline');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 2, 'count' => 100));
    }

    // =========================================================================
    public function test_userTimeline() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('userTimeline');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/user_timeline');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
    }

    // =========================================================================
    public function test_mentions() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('mentions');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/mentions');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
    }

    // =========================================================================
    public function test_retweetedByMe() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('retweetedByMe');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweeted_by_me');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
    }

    // =========================================================================
    public function test_retweetedToMe() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('retweetedToMe');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweeted_to_me');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
    }

    // =========================================================================
    public function test_retweetsOfMe() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('retweetsOfMe');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweets_of_me');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
    }

    // =========================================================================
    public function test_show() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('show', array('id' => '1234567'));
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/show/1234567');
        $this->assertIdentical($this->Status->request['uri']['query'], array());
    }

    // =========================================================================
    public function test_retweets() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('retweets', array('id' => '1234567'));
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweets/1234567');
        $this->assertIdentical($this->Status->request['uri']['query'], array());
    }

    // =========================================================================
    public function test_retweetedBy() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('retweetedBy', array('id' => '1234567'));
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/1234567/retweeted_by');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 100));
    }

    // =========================================================================
    public function test_retweetedByIds() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->find('retweetedByIds', array('id' => '1234567'));
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/1234567/retweeted_by/ids');
        $this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 100));
    }

    // =========================================================================
    public function test_tweet() {
        $this->Status->getDataSource()->expectOnce('request');
        $data = array(
            'TwimStatus' => array(
                'text' => 'test tweet',
            ),
        );
        $this->Status->tweet($data);
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/update');
        $this->assertIdentical($this->Status->request['method'], 'POST');
        $this->assertIdentical($this->Status->request['body'], array('status' => 'test tweet'));
    }

    public function test_tweet_string_params() {
        $this->Status->getDataSource()->expectOnce('request');
        $data = 'test tweet';
        $this->Status->tweet($data);
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/update');
        $this->assertIdentical($this->Status->request['method'], 'POST');
        $this->assertIdentical($this->Status->request['body'], array('status' => 'test tweet'));
    }

    public function test_tweet_simple_array() {
        $this->Status->getDataSource()->expectOnce('request');
        $data = array(
            'text' => 'test tweet',
        );
        $this->Status->tweet($data);
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/update');
        $this->assertIdentical($this->Status->request['method'], 'POST');
        $this->assertIdentical($this->Status->request['body'], array('status' => 'test tweet'));
    }

    public function test_tweet_reply() {
        $this->Status->getDataSource()->expectOnce('request');
        $data = array(
            'text' => 'test tweet',
            'in_reply_to_status_id' => '1234567890',
        );
        $this->Status->tweet($data);
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/update');
        $this->assertIdentical($this->Status->request['method'], 'POST');
        $this->assertEqual($this->Status->request['body'], array('status' => 'test tweet', 'in_reply_to_status_id' => '1234567890'));
    }

    // =========================================================================
    public function test_retweet() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->retweet('1234567');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweet/1234567');
        $this->assertIdentical($this->Status->request['method'], 'POST');
    }

    // =========================================================================
    public function test_save() {
        $this->Status->getDataSource()->expectOnce('request');
        $data = array(
            'TwimStatus' => array(
                'text' => 'test tweet',
            ),
        );
        $this->Status->save($data);
        $this->assertIdentical($this->Status->request['method'], 'POST');
        $this->assertIdentical($this->Status->request['auth'], true);
    }

    // =========================================================================
    public function test_delete() {
        $this->Status->getDataSource()->expectOnce('request');
        $this->Status->delete('1234567');
        $this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/destroy/1234567');
        $this->assertIdentical($this->Status->request['method'], 'POST');
    }

}
