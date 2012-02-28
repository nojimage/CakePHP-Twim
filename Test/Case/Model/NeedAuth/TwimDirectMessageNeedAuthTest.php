<?php

/**
 * test TwimDirectMessage (need Auth)
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
 * @since     File available since Release 2.0
 *
 *
 */
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 * @property TwimDirectMessage $DirectMessage
 */
class TwimDirectMessageNeedAuthTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->DirectMessage = ClassRegistry::init('Twim.TwimDirectMessage');
	}

	public function tearDown() {
		unset($this->DirectMessage);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testSendAndDelete() {
		$data = array(
			'TwimDirectMessage' => array(
				'screen_name' => 'nojimage',
				'text' => 'test message ' . time(),
			),
		);

		$result = $this->DirectMessage->save($data);
		$this->assertNotEmpty($result['TwimDirectMessage']);

		$result = $this->DirectMessage->find('show', array('id' => $this->DirectMessage->getLastInsertID()));
		$this->assertSame($data['TwimDirectMessage']['text'], $result['text']);

		$this->assertTrue($this->DirectMessage->delete($this->DirectMessage->getLastInsertID()));
	}

}
