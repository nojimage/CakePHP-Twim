<?php

/**
 * test TwimStatus
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

	public function testHomeTimeline() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('homeTimeline');
		$this->assertSame('1/statuses/home_timeline', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'page' => 1), $this->Status->request['uri']['query']);
	}

	public function testHomeTimeline_with_pageCount() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('homeTimeline', array('page' => 2, 'count' => 100));
		$this->assertSame('1/statuses/home_timeline', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 100, 'page' => 2), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testUserTimeline() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('userTimeline');
		$this->assertSame('1/statuses/user_timeline', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'page' => 1), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testMentions() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('mentions');
		$this->assertSame('1/statuses/mentions', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'page' => 1), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testRetweetedByMe() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetedByMe');
		$this->assertSame('1/statuses/retweeted_by_me', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'page' => 1), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testRetweetedToMe() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetedToMe');
		$this->assertSame('1/statuses/retweeted_to_me', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'page' => 1), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testRetweetsOfMe() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetsOfMe');
		$this->assertSame('1/statuses/retweets_of_me', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'page' => 1), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testShow() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('show', array('id' => '1234567'));
		$this->assertSame($this->Status->request['uri']['path'], '1/statuses/show/1234567');
		$this->assertSame($this->Status->request['uri']['query'], array());
	}

	// =========================================================================

	public function testRetweets() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweets', array('id' => '1234567'));
		$this->assertSame('1/statuses/retweets/1234567', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 100), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testRetweetedBy() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetedBy', array('id' => '1234567'));
		$this->assertSame('1/statuses/1234567/retweeted_by', $this->Status->request['uri']['path']);
		$this->assertSame(array('page' => 1, 'count' => 100), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testRetweetedByIds() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('retweetedByIds', array('id' => '1234567'));
		$this->assertSame('1/statuses/1234567/retweeted_by/ids', $this->Status->request['uri']['path']);
		$this->assertSame(array('page' => 1, 'count' => 100), $this->Status->request['uri']['query']);
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
		$this->assertSame('1/statuses/update', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
		$this->assertSame(array('status' => 'test tweet'), $this->Status->request['body']);
	}

	public function testTweet_string_params() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = 'test tweet';
		$this->Status->tweet($data);
		$this->assertSame('1/statuses/update', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
		$this->assertSame(array('status' => 'test tweet'), $this->Status->request['body']);
	}

	public function testTweet_simple_array() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'text' => 'test tweet',
		);
		$this->Status->tweet($data);
		$this->assertSame('1/statuses/update', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
		$this->assertSame(array('status' => 'test tweet'), $this->Status->request['body']);
	}

	public function testTweet_reply() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'text' => 'test tweet',
			'in_reply_to_status_id' => '1234567890',
		);
		$this->Status->tweet($data);
		$this->assertSame('1/statuses/update', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
		$this->assertEquals(array('in_reply_to_status_id' => '1234567890', 'status' => 'test tweet'), $this->Status->request['body']);
	}

	// =========================================================================

	public function testRetweet() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->retweet('1234567');
		$this->assertSame('1/statuses/retweet/1234567', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
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
		$this->assertSame('POST', $this->Status->request['method']);
		$this->assertSame(true, $this->Status->request['auth']);
	}

	// =========================================================================

	public function testDelete() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->delete('1234567');
		$this->assertSame('1/statuses/destroy/1234567', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
	}

}
