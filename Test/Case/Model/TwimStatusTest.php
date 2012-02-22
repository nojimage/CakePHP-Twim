<?php

/**
 * test TwimStatus
 *
 * PHP versions 5
 *
 * Copyright 2012, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @version   2.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2012 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link    　http://php-tips.com/
 * @since   　File available since Release 1.0
 *
 */
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 *
 * @property TwimStatus $Status
 */
class TwimStatusTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Status = ClassRegistry::init('Twim.TwimStatus');
		$this->Status->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->Status);
		parent::tearDown();
	}

	// =========================================================================

	public function testPublicTimeline() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('publicTimeline');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/public_timeline');
		$this->assertIdentical($this->Status->request['uri']['query'], array());
	}

	public function testPublicTimeline_real() {
		$this->Status->setDataSource($this->testDatasourceName);
		$results = $this->Status->find('publicTimeline');
		$this->assertIdentical(count($results), 20);
		$this->assertIdentical(count(Set::extract('/text', $results)), 20);
	}

	// =========================================================================

	public function testHomeTimeline() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('homeTimeline');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/home_timeline');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
	}

	public function testHomeTimeline_with_pageCount() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('homeTimeline', array('page' => 2, 'count' => 100));
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/home_timeline');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 2, 'count' => 100));
	}

	// =========================================================================

	public function testUserTimeline() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('userTimeline');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/user_timeline');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
	}

	// =========================================================================

	public function testMentions() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('mentions');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/mentions');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
	}

	// =========================================================================

	public function testRetweetedByMe() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetedByMe');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweeted_by_me');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
	}

	// =========================================================================

	public function testRetweetedToMe() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetedToMe');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweeted_to_me');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
	}

	// =========================================================================

	public function testRetweetsOfMe() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetsOfMe');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweets_of_me');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 200));
	}

	// =========================================================================

	public function testShow() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('show', array('id' => '1234567'));
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/show/1234567');
		$this->assertIdentical($this->Status->request['uri']['query'], array());
	}

	// =========================================================================

	public function testRetweets() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweets', array('id' => '1234567'));
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweets/1234567');
		$this->assertIdentical($this->Status->request['uri']['query'], array());
	}

	// =========================================================================

	public function testRetweetedBy() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetedBy', array('id' => '1234567'));
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/1234567/retweeted_by');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 100));
	}

	// =========================================================================

	public function testRetweetedByIds() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetedByIds', array('id' => '1234567'));
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/1234567/retweeted_by/ids');
		$this->assertIdentical($this->Status->request['uri']['query'], array('page' => 1, 'count' => 100));
	}

	// =========================================================================

	public function testTweet() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
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

	public function testTweet_string_params() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = 'test tweet';
		$this->Status->tweet($data);
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/update');
		$this->assertIdentical($this->Status->request['method'], 'POST');
		$this->assertIdentical($this->Status->request['body'], array('status' => 'test tweet'));
	}

	public function testTweet_simple_array() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'text' => 'test tweet',
		);
		$this->Status->tweet($data);
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/update');
		$this->assertIdentical($this->Status->request['method'], 'POST');
		$this->assertIdentical($this->Status->request['body'], array('status' => 'test tweet'));
	}

	public function testTweet_reply() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
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

	public function testRetweet() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->retweet('1234567');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/retweet/1234567');
		$this->assertIdentical($this->Status->request['method'], 'POST');
	}

	// =========================================================================

	public function testSave() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'TwimStatus' => array(
				'status' => 'test tweet',
			),
		);
		$this->Status->save($data);
		$this->assertIdentical($this->Status->request['method'], 'POST');
		$this->assertIdentical($this->Status->request['auth'], true);
	}

	// =========================================================================

	public function testDelete() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->delete('1234567');
		$this->assertIdentical($this->Status->request['uri']['path'], '1/statuses/destroy/1234567');
		$this->assertIdentical($this->Status->request['method'], 'POST');
	}

}
