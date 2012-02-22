<?php

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
	}

	// =========================================================================

	public function testLinkify_username() {
		$value = '@username';
		$result = '<a href="http://twitter.com/username">@username</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
		$this->assertEqual($this->Twitter->linkify($value, array('username' => false)), $value);
	}

	public function testLinkify_hashtag() {
		$value = '#hashtag';
		$result = '<a href="http://search.twitter.com/search?q=%23hashtag">#hashtag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
		$this->assertEqual($this->Twitter->linkify($value, array('hashtag' => false)), $value);
	}

	public function testLinkify_url() {
		$value = 'http://example.com';
		$result = '<a href="http://example.com">http://example.com</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
		$this->assertEqual($this->Twitter->linkify($value, array('url' => false)), $value);
	}

	public function testLinkify_username_and_hashtag() {
		$value = '@username #hashtag';
		$result = '<a href="http://twitter.com/username">@username</a> <a href="http://search.twitter.com/search?q=%23hashtag">#hashtag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_username_and_hashtag_no_space() {
		$value = '@username#hashtag';
		$result = '<a href="http://twitter.com/username">@username</a><a href="http://search.twitter.com/search?q=%23hashtag">#hashtag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_username_and_url() {
		$value = '@username http://example.com';
		$result = '<a href="http://twitter.com/username">@username</a> <a href="http://example.com">http://example.com</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_url_and_hashtag() {

		$value = 'http://example.com #hashtag';
		$result = '<a href="http://example.com">http://example.com</a> <a href="http://search.twitter.com/search?q=%23hashtag">#hashtag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_url_with_anchor() {
		$value = 'http://example.com/#hashtag';
		$result = '<a href="http://example.com/#hashtag">http://example.com/#hashtag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_username_underbar() {
		$value = '@user_name';
		$result = '<a href="http://twitter.com/user_name">@user_name</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_hashtag_underbar() {
		$value = '#hash_tag';
		$result = '<a href="http://search.twitter.com/search?q=%23hash_tag">#hash_tag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_username_another_string() {
		$value = '@user%name';
		$result = '<a href="http://twitter.com/user">@user</a>%name';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_fullurl() {
		$value = 'http://example.com:8080/path?query=search&order=asc#hashtag';
		$result = '<a href="http://example.com:8080/path?query=search&order=asc#hashtag">http://example.com:8080/path?query=search&order=asc#hashtag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_fullurl2() {
		$value = 'http://subdomain.example.com:8080/?query=search&order=asc#hashtag';
		$result = '<a href="http://subdomain.example.com:8080/?query=search&order=asc#hashtag">http://subdomain.example.com:8080/?query=search&order=asc#hashtag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_fullurl3() {
		$value = 'http://subdomain.example.com:8080/?#hashtag';
		$result = '<a href="http://subdomain.example.com:8080/?#hashtag">http://subdomain.example.com:8080/?#hashtag</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_double_username() {
		$value = '@username @nameuser';
		$result = '<a href="http://twitter.com/username">@username</a> <a href="http://twitter.com/nameuser">@nameuser</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	public function testLinkify_double_hashtag() {
		$value = '#hashtag #taghash';
		$result = '<a href="http://search.twitter.com/search?q=%23hashtag">#hashtag</a> <a href="http://search.twitter.com/search?q=%23taghash">#taghash</a>';
		$this->assertEqual($this->Twitter->linkify($value), $result);
	}

	// =========================================================================

	public function testTweetButton() {
		$view = ClassRegistry::getObject('view');
		/* @var $view View */

		$result = $this->Twitter->tweetButton();
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$this->assertEqual($result, $ok, 'default call %s');
		$this->View->expects($this->any())->method('addScript')
			->with($this->matchesRegularExpression('!' . preg_quote('http://platform.twitter.com/widgets.js', '!') . '!'));
	}

	public function testTweetButton_null_label() {
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton(null);
		$this->assertEqual($result, $ok, 'null label');
	}

	public function testTweetButton_empty_label() {
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton('');
		$this->assertEqual($result, $ok, 'empty label');
	}

	public function testTweetButton_empty_option() {
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton(null, null);
		$this->assertEqual($result, $ok, 'empty option');
	}

	public function testTweetButton_null_query_flag() {
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton(null, null, null);
		$this->assertEqual($result, $ok, 'null query flag');
	}

	public function testTweetButton_null_inline_flag() {
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton(null, null, null, null);
		$this->assertEqual($result, $ok, 'null inline flag');
	}

	function testTweetButton_test_label() {
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">TestLabel</a>
OUTPUT_EOL;
		$result = $this->Twitter->tweetButton('TestLabel');
		$this->assertEqual($result, $ok, 'Test label');
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
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?url=testUrl&amp;via=testVia&amp;text=testText&amp;related=testRelated&amp;count=horizontal&amp;lang=ja&amp;counturl=testCounturl" class="testClass">Tweet</a>
OUTPUT_EOL;
		$this->assertEqual($result, $ok, 'Test Options');
	}

	public function testTweetButton_test_options_count_none() {
		$options = array(
			'count' => 'none',
		);
		$result = $this->Twitter->tweetButton(null, $options);
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=none&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$this->assertEqual($result, $ok, 'Test Options');
	}

	public function testTweetButton_test_options_count_vertical() {
		$options = array(
			'count' => 'vertical',
		);
		$result = $this->Twitter->tweetButton(null, $options);
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=vertical&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$this->assertEqual($result, $ok, 'Test Options');
	}

	public function testTweetButton_options_count_top() {
		$options = array(
			'count' => 'top',
		);
		$result = $this->Twitter->tweetButton(null, $options);
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=none&amp;lang=en" class="twitter-share-button">Tweet</a>
OUTPUT_EOL;
		$this->assertEqual($result, $ok, 'Test Options');
	}

	public function testTweetButton_data_attributes() {
		$result = $this->Twitter->tweetButton(null, null, true);
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-lang="en">Tweet</a>
OUTPUT_EOL;
		$this->assertEqual($result, $ok, 'Test Options');
	}

	public function testTweetButton_inline() {
		$result = $this->Twitter->tweetButton(null, null, null, true);
		$ok = <<<OUTPUT_EOL
<a href="http://twitter.com/share?count=horizontal&amp;lang=en" class="twitter-share-button">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
OUTPUT_EOL;
		$this->assertEqual($result, $ok, 'inline call');
	}

}
