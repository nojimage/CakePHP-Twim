<?php

/**
 * test TwitterHelper
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
App::uses('View', 'View');
App::uses('TwitterHelper', 'Twim.View/Helper');

/**
 * @property TwitterHelper $Twitter
 */
class TwitterHelperTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->View = $this->getMock('View', array('addScript'), array(null));
		$this->Twitter = new TwitterHelper($this->View);
	}

	public function tearDown() {
		unset($this->Twitter);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testLinkify_username() {
		$value = '@username';
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'username',
			'attributes' => array(
				'href' => 'https://twitter.com/username',
				'class' => 'username',
			),
			), $this->Twitter->linkify($value));
		$this->assertSame($value, $this->Twitter->linkify($value, array('username' => false)));
	}

	public function testLinkify_hashtag() {
		$value = '#hashtag';
		$this->assertTag(array(
			'tag' => 'a',
			'content' => '#hashtag',
			'attributes' => array(
				'href' => 'https://twitter.com/#!/search?q=%23hashtag',
			),
			), $this->Twitter->linkify($value));
		$this->assertSame($value, $this->Twitter->linkify($value, array('hashtag' => false)));
	}

	public function testLinkify_url() {
		$value = 'http://example.com';
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'http://example.com',
			'attributes' => array(
				'href' => 'http://example.com',
			),
			), $this->Twitter->linkify($value));
		$this->assertSame($value, $this->Twitter->linkify($value, array('url' => false)));
	}

	public function testLinkify_username_and_hashtag() {
		$value = '@username #hashtag';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'username',
			'attributes' => array(
				'href' => 'https://twitter.com/username',
				'class' => 'username',
			),
			), $result);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => '#hashtag',
			'attributes' => array(
				'href' => 'https://twitter.com/#!/search?q=%23hashtag',
			),
			), $result);
	}

	public function testLinkify_username_and_url() {
		$value = '@username http://example.com';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'username',
			'attributes' => array(
				'href' => 'https://twitter.com/username',
				'class' => 'username',
			),
			), $result);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'http://example.com',
			'attributes' => array(
				'href' => 'http://example.com',
			),
			), $result);
	}

	public function testLinkify_url_and_hashtag() {
		$value = 'http://example.com #hashtag';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => '#hashtag',
			'attributes' => array(
				'href' => 'https://twitter.com/#!/search?q=%23hashtag',
			),
			), $result);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'http://example.com',
			'attributes' => array(
				'href' => 'http://example.com',
			),
			), $result);
	}

	public function testLinkify_url_with_anchor() {
		$value = 'http://example.com/#hashtag';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'http://example.com/#hashtag',
			'attributes' => array(
				'href' => 'http://example.com/#hashtag',
			),
			), $result);
	}

	public function testLinkify_username_underbar() {
		$value = '@user_name';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'user_name',
			'attributes' => array(
				'href' => 'https://twitter.com/user_name',
			),
			), $result);
	}

	public function testLinkify_hashtag_underbar() {
		$value = '#hash_tag';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => '#hash_tag',
			'attributes' => array(
				'href' => 'https://twitter.com/#!/search?q=%23hash_tag',
			),
			), $result);
	}

	public function testLinkify_username_another_string() {
		$value = '@user%name';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'user',
			'attributes' => array(
				'href' => 'https://twitter.com/user',
			),
			), $result);
	}

	public function testLinkify_fullurl() {
		$value = 'http://example.com:8080/path?query=search&order=asc#hashtag';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'http://example.com:8080/path?query=search&order=asc#hashtag',
			'attributes' => array(
				'href' => 'http://example.com:8080/path?query=search&order=asc#hashtag',
			),
			), $result);
	}

	public function testLinkify_fullurl2() {
		$value = 'http://subdomain.example.com:8080/?query=search&order=asc#hashtag';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'http://subdomain.example.com:8080/?query=search&order=asc#hashtag',
			'attributes' => array(
				'href' => 'http://subdomain.example.com:8080/?query=search&order=asc#hashtag',
			),
			), $result);
	}

	public function testLinkify_double_username() {
		$value = '@username @nameuser';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'username',
			'attributes' => array(
				'href' => 'https://twitter.com/username',
			),
			), $result);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => 'nameuser',
			'attributes' => array(
				'href' => 'https://twitter.com/nameuser',
			),
			), $result);
	}

	public function testLinkify_double_hashtag() {
		$value = '#hashtag #taghash';
		$result = $this->Twitter->linkify($value);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => '#hashtag',
			'attributes' => array(
				'href' => 'https://twitter.com/#!/search?q=%23hashtag',
			),
			), $result);
		$this->assertTag(array(
			'tag' => 'a',
			'content' => '#taghash',
			'attributes' => array(
				'href' => 'https://twitter.com/#!/search?q=%23taghash',
			),
			), $result);
	}

	// =========================================================================

	public function testTweetButton() {
		$this->View->expects($this->any())->method('addScript')
			->with($this->matchesRegularExpression('!' . preg_quote('http://platform.twitter.com/widgets.js', '!') . '!'));

		$result = $this->Twitter->tweetButton();
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_null_label() {
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton(null);
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_empty_label() {
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton('');
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_empty_option() {
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton(null, null);
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_null_query_flag() {
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton(null, null, null);
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_null_inline_flag() {
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton(null, null, null, null);
		$this->assertEquals($expected, $result);
	}

	function testTweetButton_test_label() {
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">TestLabel</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton('TestLabel');
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_test_options_full() {
		$options = array(
			'class' => 'testClass',
			'url' => 'testUrl',
			'via' => 'testVia',
			'text' => 'testText',
			'related' => 'testRelated',
			'lang' => 'ja',
			'counturl' => 'testCounturl',
		);
		$result = $this->Twitter->tweetButton(null, $options);
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?url=testUrl&amp;via=testVia&amp;text=testText&amp;related=testRelated&amp;count=horizontal&amp;lang=ja&amp;counturl=testCounturl" class="testClass">Tweet</a>
OUTPUT_EOL;
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_test_options_count_none() {
		$options = array(
			'count' => 'none',
		);
		$result = $this->Twitter->tweetButton(null, $options);
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=none&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_test_options_count_vertical() {
		$options = array(
			'count' => 'vertical',
		);
		$result = $this->Twitter->tweetButton(null, $options);
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=vertical&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_options_count_top() {
		$options = array(
			'count' => 'top',
		);
		$result = $this->Twitter->tweetButton(null, $options);
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=none&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_data_attributes() {
		$result = $this->Twitter->tweetButton(null, null, true);
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-lang="en">Tweet</a>
OUTPUT_EOL;
		$this->assertEquals($expected, $result);
	}

	public function testTweetButton_inline() {
		$result = $this->Twitter->tweetButton(null, null, null, true);
		$expected = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
OUTPUT_EOL;
		$this->assertEquals($expected, $result);
	}

}
