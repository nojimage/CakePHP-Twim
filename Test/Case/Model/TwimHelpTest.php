<?php

/**
 * test TwimHelp
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
App::uses('TwimHelp', 'Twim.Model');
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 *
 * @property TwimHelp $Help
 */
class TwimHelpTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Help = ClassRegistry::init('Twim.TwimHelp');
		$this->Help->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->Help);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testFindConfiguration() {
		$this->Help->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$result = $this->Help->find(TwimHelp::FINDTYPE_CONFIGURATION);

		$this->assertSame('1.1/help/configuration', $this->Help->request['uri']['path']);
	}

	public function testFindLanguages() {
		$this->Help->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$result = $this->Help->find(TwimHelp::FINDTYPE_LANGUAGES);

		$this->assertSame('1.1/help/languages', $this->Help->request['uri']['path']);
	}

	public function testFindPrivacy() {
		$this->Help->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('privacy' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit')));
		$result = $this->Help->find(TwimHelp::FINDTYPE_PRIVACY);

		$this->assertSame('1.1/help/privacy', $this->Help->request['uri']['path']);
		$this->assertSame('Lorem ipsum dolor sit amet, consectetur adipisicing elit', $result);
	}

	public function testFindTos() {
		$this->Help->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('tos' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit')));
		$result = $this->Help->find(TwimHelp::FINDTYPE_TOS);

		$this->assertSame('1.1/help/tos', $this->Help->request['uri']['path']);
		$this->assertSame('Lorem ipsum dolor sit amet, consectetur adipisicing elit', $result);
	}

}
