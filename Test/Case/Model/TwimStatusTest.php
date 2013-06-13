<?php

/**
 * test TwimStatus
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
		ob_flush();
	}

	// =========================================================================

	public function testHomeTimeline() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find(TwimStatus::FINDTYPE_HOME_TIMELINE);
		$this->assertSame('1.1/statuses/home_timeline', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200), $this->Status->request['uri']['query']);
	}

/**
 * create dummy response
 *
 * @return array
 */
	protected function _timelineDummyData($base, $count = 100) {
		$response = array();
		for ($i = 0; $i < $count; $i++) {
			$response[] = array('id' => $base - $i, 'id_str' => ($base - $i) . '');
		}
		return $response;
	}

	public function testHomeTimelineUsingMaxId() {
		$this->Status->getDataSource()->expects($this->at(0))->method('request')
			->will($this->returnValue($this->_timelineDummyData(18700688200, 100)));
		$this->Status->getDataSource()->expects($this->at(1))->method('request')
			->will($this->returnValue(array()));

		$this->Status->find(TwimStatus::FINDTYPE_HOME_TIMELINE, array('count' => 100));
		$this->assertSame('1.1/statuses/home_timeline', $this->Status->request['uri']['path']);
		$this->assertEquals(array('count' => 100, 'max_id' => '18700688100'), $this->Status->request['uri']['query']);
	}

	public function testHomeTimelineUsingSinceId() {
		$this->Status->getDataSource()->expects($this->at(0))->method('request')
			->will($this->returnValue($this->_timelineDummyData(18700688200, 200)));
		$this->Status->getDataSource()->expects($this->at(1))->method('request')
			->will($this->returnValue(array()));

		$this->Status->find(TwimStatus::FINDTYPE_HOME_TIMELINE, array('since_id' => '18700688001'));
		$this->assertSame('1.1/statuses/home_timeline', $this->Status->request['uri']['path']);
		$this->assertEquals(array('count' => 200, 'since_id' => '18700688201'), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testUserTimeline() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find(TwimStatus::FINDTYPE_USER_TIMELINE);
		$this->assertSame('1.1/statuses/user_timeline', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testMentions() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find('mentions');
		$this->assertSame('1.1/statuses/mentions_timeline', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200), $this->Status->request['uri']['query']);
	}

	public function testMentionsTimeline() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find(TwimStatus::FINDTYPE_MENTIONS_TIMELINE);
		$this->assertSame('1.1/statuses/mentions_timeline', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testRetweetsOfMe() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find(TwimStatus::FINDTYPE_RETWEETS_OF_ME);
		$this->assertSame('1.1/statuses/retweets_of_me', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 200), $this->Status->request['uri']['query']);
	}

	// =========================================================================

	public function testShow() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find(TwimStatus::FINDTYPE_SHOW, array('id' => '1234567'));
		$this->assertSame($this->Status->request['uri']['path'], '1.1/statuses/show/1234567');
		$this->assertSame($this->Status->request['uri']['query'], array());
	}

	// =========================================================================

	public function testRetweets() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->find(TwimStatus::FINDTYPE_RETWEETS, array('id' => '1234567'));
		$this->assertSame('1.1/statuses/retweets/1234567', $this->Status->request['uri']['path']);
		$this->assertSame(array('count' => 100), $this->Status->request['uri']['query']);
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
		$this->assertSame('1.1/statuses/update', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
		$this->assertSame(array('status' => 'test tweet'), $this->Status->request['body']);
	}

	public function testTweet_string_params() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = 'test tweet';
		$this->Status->tweet($data);
		$this->assertSame('1.1/statuses/update', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
		$this->assertSame(array('status' => 'test tweet'), $this->Status->request['body']);
	}

	public function testTweet_simple_array() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'text' => 'test tweet',
		);
		$this->Status->tweet($data);
		$this->assertSame('1.1/statuses/update', $this->Status->request['uri']['path']);
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
		$this->assertSame('1.1/statuses/update', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
		$this->assertEquals(array('in_reply_to_status_id' => '1234567890', 'status' => 'test tweet'), $this->Status->request['body']);
	}

	// =========================================================================

	public function testRetweet() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Status->retweet('1234567');
		$this->assertSame('1.1/statuses/retweet/1234567', $this->Status->request['uri']['path']);
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
		$this->assertSame('1.1/statuses/destroy/1234567', $this->Status->request['uri']['path']);
		$this->assertSame('POST', $this->Status->request['method']);
	}

	// =========================================================================

	public function testOembed() {
		$this->Status->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$result = $this->Status->find('oembed', array('id' => '1234567'));
		$this->assertSame('1.1/statuses/oembed', $this->Status->request['uri']['path']);
		$this->assertSame(array('id' => '1234567'), $this->Status->request['uri']['query']);
	}

}
