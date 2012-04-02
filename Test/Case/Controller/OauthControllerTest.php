<?php

/**
 * Oauth Controller Test Case
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
App::uses('OauthController', 'Twim.Controller');

/**
 *
 */
class OauthControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testCallbackGetDenied() {
		$c = $this->generate('Twim.Oauth', array(
			'methods' => array(
				'redirect'
			),)
		);

		$c->expects($this->once())
			->method('redirect')
			->with(array(
				'plugin' => 'twim',
				'controller' => 'oauth',
				'action' => 'login',
				'admin' => false,
			));

		$this->testAction('/twim/oauth/callback', array(
			'method' => 'get',
			'data' => array('denied' => 'somekey'),
		));
	}

}
