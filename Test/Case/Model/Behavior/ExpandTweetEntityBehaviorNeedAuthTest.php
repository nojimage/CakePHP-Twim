<?php

/**
 * Twitter Authenticatable Behavior Test Case
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
 * @property TwimSearch $Search
 * @property TwimStatus $Status
 */
class ExpandTweetEntityBehaviorNeedAuthTest extends TwimConnectionTestCase {

	public $fixtures = array();

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->Search = ClassRegistry::init('Twim.TwimSearch');
		$this->Status = ClassRegistry::init('Twim.TwimStatus');
		$this->Search->setDataSource('twitter');
		$this->Status->setDataSource('twitter');
	}

	public function tearDown() {
		unset($this->Search);
		unset($this->Status);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testAfterFind() {
		$this->Search->setExpandUrl()->setExpandHashtag();
		$results = $this->Search->find('search', array('q' => '#cakephp http://', 'limit' => 20));
		$this->assertRegExp('/class="twitter-timeline-link" rel="external nofollow"/', $results[0]['expanded_text']);
		$this->assertRegExp('/ class="twitter-hashtag" rel="external nofollow"/', $results[0]['expanded_text']);
	}

	public function testAfterFind_with_media() {
		$this->Search->setExpandUrl()->setExpandHashtag();
		$results = $this->Search->find('search', array('q' => 'pic.twitter.com -RT', 'limit' => 20));
		$this->assertRegExp('/class="twitter-timeline-link" rel="external nofollow"/', $results[0]['expanded_text']);
	}

	public function testAfterFind_Status() {
		$this->Status->setExpandUrl()->setExpandHashtag();
		$results = $this->Status->find('show', array('id' => '121055461549158400'));
		$this->assertRegExp('/class="twitter-timeline-link" rel="external nofollow"/', $results['expanded_text']);
		$this->assertRegExp('/ class="twitter-hashtag" rel="external nofollow"/', $results['expanded_text']);
	}

	public function testAfterFind_Status_urlString() {
		$this->Status->setExpandHashtag()->setExpandUrl('string');
		$results = $this->Status->find('show', array('id' => '121055461549158400'));
		$this->assertNotRegExp('/class="twitter-timeline-link" rel="external nofollow"/', $results['expanded_text']);
		$this->assertRegExp('!http://ask.cakephp.org/s/1yu!', $results['expanded_text']);
	}

	public function testAfterFind_overrideText() {
		$this->Search->setExpandHashtag()->setExpandUrl()->setOverrideText();
		$results = $this->Search->find('search', array('q' => '#cakephp http://', 'limit' => 20));
		$this->assertRegExp('/class="twitter-timeline-link" rel="external nofollow"/', $results[0]['text']);
		$this->assertRegExp('/ class="twitter-hashtag" rel="external nofollow"/', $results[0]['text']);
	}

}
