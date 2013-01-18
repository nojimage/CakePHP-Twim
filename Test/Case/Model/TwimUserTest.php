<?php

/**
 * test TwimUser
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
App::uses('TwimUser', 'Twim.Model');

/**
 *
 * @property TwimUser $User
 */
class TwimUserTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('Twim.TwimUser');
		$this->User->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->User);
		parent::tearDown();
	}

	// =========================================================================

	public function testLookup() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->assertFalse($this->User->find(TwimUser::FINDTYPE_LOOKUP));
	}

	public function testLookup_user_id() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find(TwimUser::FINDTYPE_LOOKUP, array('user_id' => '1234'));
		$this->assertSame('1.1/users/lookup', $this->User->request['uri']['path']);
		$this->assertSame(array('user_id' => '1234'), $this->User->request['uri']['query']);
	}

	public function testLookup_array_user_id() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find('lookup', array('user_id' => array('1234', '5678', '9876')));
		$this->assertSame('1.1/users/lookup', $this->User->request['uri']['path']);
		$this->assertSame(array('user_id' => '1234,5678,9876'), $this->User->request['uri']['query']);
	}

	public function testLookup_screen_name() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find('lookup', array('screen_name' => 'abcd'));
		$this->assertSame('1.1/users/lookup', $this->User->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'abcd'), $this->User->request['uri']['query']);
	}

	public function testLookup_array_screen_name() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find('lookup', array('screen_name' => array('abcd', 'efgh', 'ijkl')));
		$this->assertSame('1.1/users/lookup', $this->User->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'abcd,efgh,ijkl'), $this->User->request['uri']['query']);
	}

	// =========================================================================

	public function testProfileImage() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->assertFalse($this->User->find('profileImage'));
	}

	public function testProfileImage_with_screen_name() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find('profileImage', array('screen_name' => 'abcd'));
		$this->assertSame('1.1/users/show', $this->User->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'abcd'), $this->User->request['uri']['query']);
	}

	public function testProfileImage_with_size() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find('profileImage', array('screen_name' => 'abcd', 'size' => 'bigger'));
		$this->assertSame('1.1/users/show', $this->User->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'abcd'), $this->User->request['uri']['query']);
	}

	// =========================================================================

	public function testSearch() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find(TwimUser::FINDTYPE_SEARCH, array('q' => 'cake'));
		$this->assertSame('1.1/users/search', $this->User->request['uri']['path']);
		$this->assertSame(array('page' => 1, 'q' => 'cake'), $this->User->request['uri']['query']);
		$this->assertSame(true, $this->User->request['auth']);
	}

	// =========================================================================

	public function testShow() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->assertFalse($this->User->find('show'));
	}

	public function testShow_user_id() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find(TwimUser::FINDTYPE_SHOW, array('user_id' => '1234'));
		$this->assertSame('1.1/users/show', $this->User->request['uri']['path']);
		$this->assertSame(array('user_id' => '1234'), $this->User->request['uri']['query']);
	}

	public function testShow_screen_name() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find('show', array('screen_name' => 'abcd'));
		$this->assertSame('1.1/users/show', $this->User->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'abcd'), $this->User->request['uri']['query']);
	}

	// =========================================================================

	public function testContributees() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->assertFalse($this->User->find('contributees'));
	}

	public function testContributees_user_id() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find(TwimUser::FINDTYPE_CONTRIBUTEES, array('user_id' => '1234'));
		$this->assertSame('1.1/users/contributees', $this->User->request['uri']['path']);
		$this->assertSame(array('user_id' => '1234'), $this->User->request['uri']['query']);
	}

	public function testContributees_screen_name() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find('contributees', array('screen_name' => 'abcd'));
		$this->assertSame('1.1/users/contributees', $this->User->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'abcd'), $this->User->request['uri']['query']);
	}

	// =========================================================================

	public function testContributors() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->assertFalse($this->User->find('contributors'));
	}

	public function testContributors_user_id() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find(TwimUser::FINDTYPE_CONTRIBUTORS, array('user_id' => '1234'));
		$this->assertSame('1.1/users/contributors', $this->User->request['uri']['path']);
		$this->assertSame(array('user_id' => '1234'), $this->User->request['uri']['query']);
	}

	public function testContributors_screen_name() {
		$this->User->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(false));
		$this->User->find('contributors', array('screen_name' => 'abcd'));
		$this->assertSame('1.1/users/contributors', $this->User->request['uri']['path']);
		$this->assertSame(array('screen_name' => 'abcd'), $this->User->request['uri']['query']);
	}

}
