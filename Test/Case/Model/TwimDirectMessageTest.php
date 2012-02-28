<?php

/**
 * test TwimDirectMessage
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
 */
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

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
		$this->DirectMessage->find('receipt');
		$this->assertSame('1/direct_messages', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('page' => 1, 'count' => 200), $this->DirectMessage->request['uri']['query']);
	}

	public function testFindAll() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find('all');
		$this->assertSame('1/direct_messages', $this->DirectMessage->request['uri']['path']);
	}

	public function testFindReceiptWithPageCount() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find('receipt', array('page' => 2, 'count' => 200));
		$this->assertSame('1/direct_messages', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('page' => 2, 'count' => 200), $this->DirectMessage->request['uri']['query']);
	}

	// =========================================================================

	public function testFindSent() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find('sent');
		$this->assertSame('1/direct_messages/sent', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('page' => 1, 'count' => 200), $this->DirectMessage->request['uri']['query']);
	}

	public function testFindSentWithPageCount() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find('sent', array('page' => 2, 'count' => 200));
		$this->assertSame('1/direct_messages/sent', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array('page' => 2, 'count' => 200), $this->DirectMessage->request['uri']['query']);
	}

	// =========================================================================

	public function testFindShow() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->find('show', array('id' => '1234'));
		$this->assertSame('1/direct_messages/show/1234', $this->DirectMessage->request['uri']['path']);
		$this->assertSame(array(), $this->DirectMessage->request['uri']['query']);
	}

	public function testFindById() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->findById('9876');
		$this->assertSame('1/direct_messages/show/9876', $this->DirectMessage->request['uri']['path']);
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
		$this->assertSame('1/direct_messages/new', $this->DirectMessage->request['uri']['path']);
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
		$this->assertSame('1/direct_messages/new', $this->DirectMessage->request['uri']['path']);
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
		$this->assertSame('1/direct_messages/new', $this->DirectMessage->request['uri']['path']);
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
		$this->assertSame('1/direct_messages/new', $this->DirectMessage->request['uri']['path']);
		$this->assertSame('POST', $this->DirectMessage->request['method']);
		$this->assertSame(array('user_id' => '1234', 'text' => 'test message to 1234'), $this->DirectMessage->request['body']);
	}

	// =========================================================================

	public function testDelete() {
		$this->DirectMessage->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->DirectMessage->delete('1234567');
		$this->assertSame('1/direct_messages/destroy/1234567', $this->DirectMessage->request['uri']['path']);
		$this->assertSame('POST', $this->DirectMessage->request['method']);
	}

}
