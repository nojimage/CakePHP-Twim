<?php

/**
 * Twitter Authenticatable Behavior Test Case
 */
App::import('Lib', 'Twim.TwimConnectionTestCase');
App::import('Model', array('Twim.TwimSearch', 'Twim.TwimStatus'));
App::import('Behavior', array('Twim.ExpandTweetEntity'));

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
    public function startTest($method) {
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
    public function endTest($method) {
        unset($this->Search);
        unset($this->Status);
        ClassRegistry::flush();
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
        $this->Search->getDataSource()->expectOnce('request');
        $this->Search->find('search', 'twitter');
        $this->assertTrue($this->Search->request['uri']['query']['include_entities']);
    }

    public function testBeforeFind_setIncludeEntities() {
        $this->Search->setExpandHashtag();
        $this->Search->getDataSource()->expectOnce('request');
        $this->Search->find('search', array('q' => 'twitter', 'include_entities' => false));
        $this->assertTrue(empty($this->Search->request['uri']['query']['include_entities']));
    }

    public function testBeforeFind_notExpand() {
        $this->Search->getDataSource()->expectOnce('request');
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
