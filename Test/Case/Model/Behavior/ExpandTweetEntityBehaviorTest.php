<?php

/**
 * Twitter Authenticatable Behavior Test Case
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
 * @property TwimSearch $Search
 * @property TwimStatus $Status
 */
class ExpandTweetEntityBehaviorTest extends TwimConnectionTestCase {

	public $fixtures = array();

	/**
	 * startTest method
	 *
	 * @access public
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->Search = ClassRegistry::init('Twim.TwimSearch');
		$this->Status = ClassRegistry::init('Twim.TwimStatus');
		$this->Search->setDataSource($this->mockDatasourceName);
		$this->Status->setDataSource($this->mockDatasourceName);
	}

	/**
	 * endTest method
	 *
	 * @access public
	 * @return void
	 */
	public function tearDown() {
		unset($this->Search);
		unset($this->Status);
		parent::tearDown();
	}

	// =========================================================================
	public function testSetup() {
		$this->assertTrue($this->Search->Behaviors->attached('ExpandTweetEntity'));
		$ok = array(
			'expandHashtag' => false,
			'expandUrl' => false,
			'overrideText' => false,
		);
		$this->assertEqual($ok, $this->Search->Behaviors->ExpandTweetEntity->settings['TwimSearch']);
		//
		$this->assertTrue($this->Status->Behaviors->attached('ExpandTweetEntity'));
		$ok = array(
			'expandHashtag' => false,
			'expandUrl' => false,
			'overrideText' => false,
		);
		$this->assertEqual($ok, $this->Status->Behaviors->ExpandTweetEntity->settings['TwimStatus']);
	}

	// =========================================================================

	public function testSetExpandHashtag() {
		$this->Search->setExpandHashtag();
		$this->assertTrue($this->Search->Behaviors->ExpandTweetEntity->settings['TwimSearch']['expandHashtag']);
	}

	public function testSetExpandHashtag_chain() {
		$this->Search->setExpandHashtag()->setExpandHashtag(false);
		$this->assertFalse($this->Search->Behaviors->ExpandTweetEntity->settings['TwimSearch']['expandHashtag']);
	}

	// =========================================================================

	public function testSetExpandUrl() {
		$this->Search->setExpandUrl();
		$this->assertTrue($this->Search->Behaviors->ExpandTweetEntity->settings['TwimSearch']['expandUrl']);
	}

	public function testSetExpandUrl_chain() {
		$this->Search->setExpandUrl()->setExpandUrl(false);
		$this->assertFalse($this->Search->Behaviors->ExpandTweetEntity->settings['TwimSearch']['expandUrl']);
	}

	// =========================================================================

	public function testSetOverrideText() {
		$this->Search->setOverrideText();
		$this->assertTrue($this->Search->Behaviors->ExpandTweetEntity->settings['TwimSearch']['overrideText']);
	}

	public function testSetOverrideText_chain() {
		$this->Search->setOverrideText()->setOverrideText(false);
		$this->assertFalse($this->Search->Behaviors->ExpandTweetEntity->settings['TwimSearch']['overrideText']);
	}

	// =========================================================================

	public function testBeforeFind() {
		$this->Search->setExpandHashtag();
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Search->find('search', 'twitter');
		$this->assertTrue($this->Search->request['uri']['query']['include_entities']);
	}

	public function testBeforeFind_setIncludeEntities() {
		$this->Search->setExpandHashtag();
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Search->find('search', array('q' => 'twitter', 'include_entities' => false));
		$this->assertTrue(empty($this->Search->request['uri']['query']['include_entities']));
	}

	public function testBeforeFind_notExpand() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array()));
		$this->Search->find('search', array('q' => 'twitter'));
		$this->assertTrue(empty($this->Search->request['uri']['query']['include_entities']));
	}

	// =========================================================================

	public function testExpandHashtag() {
		$tweet = array(
			'text' => 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question',
			'entities' => array(
				'hashtags' => array(
					array(
						'text' => 'cakephp',
						'indices' => array(78, 86)
					),
					array(
						'text' => 'question',
						'indices' => array(87, 96)
					),
				),
				'urls' => array(
					array(
						'url' => 'http://t.co/VKUESCJp',
						'expanded_url' => 'http://ask.cakephp.org/s/1yu',
						'display_url' => 'ask.cakephp.org/s/1yu',
						'indices' => array(56, 76)
					),
				),
			),
		);
		$ok = 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  <a href="http://twitter.com/#!/search?q=%23cakephp" title="#cakephp" class="twitter-hashtag" rel="external nofollow">#cakephp</a> <a href="http://twitter.com/#!/search?q=%23question" title="#question" class="twitter-hashtag" rel="external nofollow">#question</a>';

		$tweet = $this->Search->expandHashtag($tweet);
		$this->assertIdentical($ok, $tweet['expanded_text']);
	}

	public function testExpandHashtag_entities_empty() {
		$tweet = array(
			'text' => 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question',
			'entities' => array(
				'hashtags' => array(
				),
				'urls' => array(
				),
			),
		);
		$ok = 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question';

		$tweet = $this->Search->expandHashtag($tweet);
		$this->assertIdentical($ok, $tweet['expanded_text']);
	}

	public function testExpandHashtag_manyhashtag() {

		$tweet = array(
			'text' => '豚骨ラーメン食べたでー。うまかった＾－＾　 #followdaibosyu  #followmejp  #sougofollow  #goen  #相互フォロー  #sougo  #フォロー  #相互',
			'entities' => array('hashtags' =>
				array(
					array(
						'indices' => array(22, 37),
						'text' => 'followdaibosyu',
					),
					array(
						'indices' => array(39, 50),
						'text' => 'followmejp',
					),
					array(
						'indices' => array(52, 64),
						'text' => 'sougofollow',
					),
					array(
						'indices' => array(66, 71),
						'text' => 'goen',
					),
					array(
						'indices' => array(73, 80),
						'text' => '相互フォロー',
					),
					array(
						'indices' => array(82, 88),
						'text' => 'sougo',
					),
					array(
						'indices' => array(90, 95),
						'text' => 'フォロー',
					),
					array(
						'indices' => array(97, 100),
						'text' => '相互',
					),
				),
			),
		);

		$tweet = $this->Search->expandHashtag($tweet);
		$ok = '豚骨ラーメン食べたでー。うまかった＾－＾　 <a href="http://twitter.com/#!/search?q=%23followdaibosyu" title="#followdaibosyu" class="twitter-hashtag" rel="external nofollow">#followdaibosyu</a>  <a href="http://twitter.com/#!/search?q=%23followmejp" title="#followmejp" class="twitter-hashtag" rel="external nofollow">#followmejp</a>  <a href="http://twitter.com/#!/search?q=%23sougofollow" title="#sougofollow" class="twitter-hashtag" rel="external nofollow">#sougofollow</a>  <a href="http://twitter.com/#!/search?q=%23goen" title="#goen" class="twitter-hashtag" rel="external nofollow">#goen</a>  <a href="http://twitter.com/#!/search?q=%23%E7%9B%B8%E4%BA%92%E3%83%95%E3%82%A9%E3%83%AD%E3%83%BC" title="#相互フォロー" class="twitter-hashtag" rel="external nofollow">#相互フォロー</a>  <a href="http://twitter.com/#!/search?q=%23sougo" title="#sougo" class="twitter-hashtag" rel="external nofollow">#sougo</a>  <a href="http://twitter.com/#!/search?q=%23%E3%83%95%E3%82%A9%E3%83%AD%E3%83%BC" title="#フォロー" class="twitter-hashtag" rel="external nofollow">#フォロー</a>  <a href="http://twitter.com/#!/search?q=%23%E7%9B%B8%E4%BA%92" title="#相互" class="twitter-hashtag" rel="external nofollow">#相互</a>';

		$this->assertEqual($ok, $tweet['expanded_text']);
	}

	// =========================================================================

	public function testExpandUrl() {
		$tweet = array(
			'text' => 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question',
			'entities' => array(
				'hashtags' => array(
					array(
						'text' => 'cakephp',
						'indices' => array(78, 86)
					),
					array(
						'text' => 'question',
						'indices' => array(87, 96)
					),
				),
				'urls' => array(
					array(
						'url' => 'http://t.co/VKUESCJp',
						'expanded_url' => 'http://ask.cakephp.org/s/1yu',
						'display_url' => 'ask.cakephp.org/s/1yu',
						'indices' => array(56, 76)
					),
				),
			),
		);
		$ok = 'How can I submit my app to the bakery (recently baked?) <a href="http://t.co/VKUESCJp" title="http://ask.cakephp.org/s/1yu" class="twitter-timeline-link" rel="external nofollow">ask.cakephp.org/s/1yu</a>  #cakephp #question';

		$tweet = $this->Search->expandUrl($tweet);
		$this->assertIdentical($ok, $tweet['expanded_text']);
	}

	public function testExpandUrl_entities_empty() {
		$tweet = array(
			'text' => 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question',
			'entities' => array(
				'hashtags' => array(
				),
				'urls' => array(
				),
			),
		);
		$ok = 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question';

		$tweet = $this->Search->expandUrl($tweet);
		$this->assertIdentical($ok, $tweet['expanded_text']);
	}

	public function testExpandUrlString() {
		$tweet = array(
			'text' => 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question',
			'entities' => array(
				'hashtags' => array(
					array(
						'text' => 'cakephp',
						'indices' => array(78, 86)
					),
					array(
						'text' => 'question',
						'indices' => array(87, 96)
					),
				),
				'urls' => array(
					array(
						'url' => 'http://t.co/VKUESCJp',
						'expanded_url' => 'http://ask.cakephp.org/s/1yu',
						'display_url' => 'ask.cakephp.org/s/1yu',
						'indices' => array(56, 76)
					),
				),
			),
		);
		$ok = 'How can I submit my app to the bakery (recently baked?) http://ask.cakephp.org/s/1yu  #cakephp #question';

		$tweet = $this->Search->expandUrlString($tweet);
		$this->assertIdentical($ok, $tweet['expanded_text']);
	}

	public function testExpandUrlString_entities_empty() {
		$tweet = array(
			'text' => 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question',
			'entities' => array(
				'hashtags' => array(
				),
				'urls' => array(
				),
			),
		);
		$ok = 'How can I submit my app to the bakery (recently baked?) http://t.co/VKUESCJp  #cakephp #question';

		$tweet = $this->Search->expandUrlString($tweet);
		$this->assertIdentical($ok, $tweet['expanded_text']);
	}

	// =========================================================================

	public function testAfterFind() {
		$this->Search->setExpandHashtag()->setExpandUrl()->setDataSource($this->testDatasourceName);

		$results = $this->Search->find('search', array('q' => '#cakephp http://', 'limit' => 20));
		$this->assertPattern('/class="twitter-timeline-link" rel="external nofollow"/', $results[0]['expanded_text']);
		$this->assertPattern('/ class="twitter-hashtag" rel="external nofollow"/', $results[0]['expanded_text']);
	}

	public function testAfterFind_Status() {
		$this->Status->setExpandHashtag()->setExpandUrl()->setDataSource($this->testDatasourceName);

		$results = $this->Status->find('show', array('id' => '121055461549158400'));
		$this->assertPattern('/class="twitter-timeline-link" rel="external nofollow"/', $results['expanded_text']);
		$this->assertPattern('/ class="twitter-hashtag" rel="external nofollow"/', $results['expanded_text']);
	}

	public function testAfterFind_Status_urlString() {
		$this->Status->setExpandHashtag()->setExpandUrl('string')->setDataSource($this->testDatasourceName);

		$results = $this->Status->find('show', array('id' => '121055461549158400'));
		$this->assertNoPattern('/class="twitter-timeline-link" rel="external nofollow"/', $results['expanded_text']);
		$this->assertPattern('!http://ask.cakephp.org/s/1yu!', $results['expanded_text']);
	}

	public function testAfterFind_overrideText() {
		$this->Search->setExpandHashtag()->setExpandUrl()->setOverrideText()->setDataSource($this->testDatasourceName);

		$results = $this->Search->find('search', array('q' => '#cakephp http://', 'limit' => 20));
		$this->assertPattern('/class="twitter-timeline-link" rel="external nofollow"/', $results[0]['text']);
		$this->assertPattern('/ class="twitter-hashtag" rel="external nofollow"/', $results[0]['text']);
	}

}
