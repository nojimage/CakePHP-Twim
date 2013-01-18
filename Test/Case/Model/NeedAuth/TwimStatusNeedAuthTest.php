<?php

/**
 * test TwimStatus (need Auth)
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
 *
 */
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 * @property TwimStatus $Status
 */
class TwimStatusNeedAuthTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->Status = ClassRegistry::init('Twim.TwimStatus');
		$this->Status->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->Status);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testTweet_and_delete() {
		$data = array(
			'TwimStatus' => array(
				'text' => 'test tweet ' . time(),
			),
		);

		$result = $this->Status->tweet($data);
		$this->assertNotEmpty($result['TwimStatus']);

		$result = $this->Status->find('show', array('id' => $this->Status->getLastInsertID()));
		$this->assertSame($data['TwimStatus']['text'], $result['text']);

		$this->assertTrue($this->Status->delete($this->Status->getLastInsertID()));
	}

	public function testHomeTimeline() {
		$results = $this->Status->find('homeTimeline', array('limit' => 300));
		$this->assertCount(300, $results);
	}

	public function testMentionsTimeline() {
		$results = $this->Status->find('mentionsTimeline', array('limit' => 300));
		$this->assertCount(300, $results);
	}

}
