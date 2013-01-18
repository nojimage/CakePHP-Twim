<?php

/**
 * test TwimDirectMessage
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
 * @since     File available since Release 2.0
 *
 */
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');
App::uses('TwimDirectMessage', 'Twim.Model');

/**
 *
 * @property TwimDirectMessage $DirectMessage
 */
class TwimDirectMessageTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->DirectMessage = ClassRegistry::init('Twim.TwimDirectMessage');
		$this->DirectMessage->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->DirectMessage);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testFindReceipt() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find(TwimDirectMessage::FINDTYPE_RECEIPT);
		$this->assertSame('1.1/direct_messages', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('page' => 1, 'count' => 200), $this->DirectMessage->request['uri']['query']);
	}

	public function testFindAll() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find('all');
		$this->assertSame('1.1/direct_messages', $this->DirectMessage->request['uri']['path']);
	}

	public function testFindReceiptWithPageCount() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find('receipt', array('page' => 2, 'count' => 200));
		$this->assertSame('1.1/direct_messages', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('page' => 2, 'count' => 200,), $this->DirectMessage->request['uri']['query']);
	}

	// =========================================================================

	public function testFindSent() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find(TwimDirectMessage::FINDTYPE_SENT);
		$this->assertSame('1.1/direct_messages/sent', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'page' => 1), $this->DirectMessage->request['uri']['query']);
	}

	public function testFindSentUsingMaxId() {
		$this->DirectMessage->getDataSource()->expects($this->at(0))->method('request')
			->will($this->returnValue(array(
					array('id' => 18700688341, 'id_str' => '18700688341'),
					array('id' => 18700688340, 'id_str' => '18700688340'),
				)));
		$this->DirectMessage->getDataSource()->expects($this->at(1))->method('request')
			->will($this->returnValue(array()));

		$this->DirectMessage->find('sent', array('count' => 200));
		$this->assertSame('1.1/direct_messages/sent', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'page' => 1, 'max_id' => 18700688339), $this->DirectMessage->request['uri']['query']);
	}

	public function testFindSentUsingSinceId() {
		$this->DirectMessage->getDataSource()->expects($this->at(0))->method('request')
			->will($this->returnValue(array(
					array('id' => 118700688341, 'id_str' => '118700688341'),
					array('id' => 118700688340, 'id_str' => '118700688340'),
				)));
		$this->DirectMessage->getDataSource()->expects($this->at(1))->method('request')
			->will($this->returnValue(array()));

		$this->DirectMessage->find('sent', array('count' => 200, 'since_id' => 18700688341));
		$this->assertSame('1.1/direct_messages/sent', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('count' => 200, 'since_id' => 118700688342, 'page' => 1), $this->DirectMessage->request['uri']['query']);
	}

	// =========================================================================

	public function testFindShow() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find(TwimDirectMessage::FINDTYPE_SHOW, array('id' => '1234'));
		$this->assertSame('1.1/direct_messages/show/1234', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array(), $this->DirectMessage->request['uri']['query']);
	}

	public function testFindById() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->findById('9876');
		$this->assertSame('1.1/direct_messages/show/9876', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array(), $this->DirectMessage->request['uri']['query']);
	}

	// =========================================================================

	public function testSave() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'TwimDirectMessage' => array(
				'user_id' => '1234',
				'text' => 'test message to 1234',
			),
		);
		$this->DirectMessage->save($data);
		$this->assertSame('1.1/direct_messages/new', $this->DirectMessage->request['uri']['path']);
		$this->assertSame('POST', $this->DirectMessage->request['method']);
		$this->assertSame(true, $this->DirectMessage->request['auth']);
		$this->assertSame(array('user_id' => '1234', 'text' => 'test message to 1234'), $this->DirectMessage->request['body']);
	}

	public function testSaveWithScreenName() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'TwimDirectMessage' => array(
				'screen_name' => 'abcd',
				'text' => 'test message to abcd',
			),
		);
		$this->DirectMessage->save($data);
		$this->assertSame('1.1/direct_messages/new', $this->DirectMessage->request['uri']['path']);
		$this->assertSame('POST', $this->DirectMessage->request['method']);
		$this->assertSame(array('screen_name' => 'abcd', 'text' => 'test message to abcd'), $this->DirectMessage->request['body']);
	}

	public function testSaveSimpleArray() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'user_id' => '1234',
			'text' => 'test message to 1234',
		);
		$this->DirectMessage->save($data);
		$this->assertSame('1.1/direct_messages/new', $this->DirectMessage->request['uri']['path']);
		$this->assertSame('POST', $this->DirectMessage->request['method']);
		$this->assertSame(array('user_id' => '1234', 'text' => 'test message to 1234'), $this->DirectMessage->request['body']);
	}

	public function testSend() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$data = array(
			'TwimDirectMessage' => array(
				'user_id' => '1234',
				'text' => 'test message to 1234',
			),
		);
		$this->DirectMessage->send($data);
		$this->assertSame('1.1/direct_messages/new', $this->DirectMessage->request['uri']['path']);
		$this->assertSame('POST', $this->DirectMessage->request['method']);
		$this->assertSame(array('user_id' => '1234', 'text' => 'test message to 1234'), $this->DirectMessage->request['body']);
	}

	// =========================================================================

	public function testDelete() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->delete('1234567');
		$this->assertSame('1.1/direct_messages/destroy/1234567', $this->DirectMessage->request['uri']['path']);
		$this->assertSame('POST', $this->DirectMessage->request['method']);
	}

}
