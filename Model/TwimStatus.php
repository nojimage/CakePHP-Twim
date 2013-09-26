<?php

/**
 * for Status API
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
 * @link      https://dev.twitter.com/docs/api/1.1/get/statuses/mentions_timeline
 * @link      https://dev.twitter.com/docs/api/1.1/get/statuses/home_timeline
 * @link      https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
 * @link      https://dev.twitter.com/docs/api/1.1/get/statuses/retweets_of_me
 * @link      https://dev.twitter.com/docs/api/1.1/get/statuses/retweets/%3Aid
 * @link      https://dev.twitter.com/docs/api/1.1/get/statuses/show/%3Aid
 * @link      https://dev.twitter.com/docs/api/1.1/post/statuses/update
 * @link      https://dev.twitter.com/docs/api/1.1/post/statuses/retweet/%3Aid
 * @link      https://dev.twitter.com/docs/api/1.1/post/statuses/destroy/%3Aid
 * @link      https://dev.twitter.com/docs/api/1.1/get/statuses/oembed
 *
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 * @method TwimStatus setExpandHashtag()
 * @method TwimStatus setExpandUrl()
 * @method array expandHashtag()
 * @method array expandUrl()
 */
class TwimStatus extends TwimAppModel {

	public $apiUrlBase = '1.1/statuses/';

/**
 * Custom find type name
 */
	const FINDTYPE_HOME_TIMELINE = 'homeTimeline';

	const FINDTYPE_USER_TIMELINE = 'userTimeline';

	const FINDTYPE_MENTIONS_TIMELINE = 'mentionsTimeline';

	const FINDTYPE_SHOW = 'show';

	const FINDTYPE_RETWEETS_OF_ME = 'retweetsOfMe';

	const FINDTYPE_RETWEETS = 'retweets';

/**
 * The model's schema. Used by FormHelper
 *
 * @var array
 */
	protected $_schema = array(
		'id' => array('type' => 'integer', 'length' => '11'),
		'status' => array('type' => 'string', 'length' => '140'),
		'in_reply_to_status_id' => array('type' => 'integer', 'length' => '11'),
		'in_reply_to_user_id' => array('type' => 'integer', 'length' => '11'),
		'in_reply_to_screen_name' => array('type' => 'string', 'length' => '255'),
	);

/**
 * Validation rules for the model
 *
 * @var array
 */
	public $validate = array(
		'status' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter some text',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 140),
				'message' => 'Text cannot exceed 140 characters',
			),
		),
		'in_reply_to_status_id' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'The ID of the status you are replying to should be numeric',
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'in_reply_to_user_id' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'The ID of the user you are replying to should be numeric',
				'required' => false,
				'allowEmpty' => true,
			),
		),
	);

/**
 *
 * @var array
 */
	public $actsAs = array(
		'Twim.ExpandTweetEntity' => array('expandHashtag' => false, 'expandUrl' => false),
	);

/**
 * Custom find types available on this model
 *
 * @var array
 */
	public $findMethods = array(
		'homeTimeline' => true,
		'userTimeline' => true,
		'mentions' => true,
		'mentionsTimeline' => true,
		'show' => true,
		'retweetsOfMe' => true,
		'retweets' => true,
		'oembed' => true,
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'homeTimeline' => array('count', 'since_id', 'max_id', 'trim_user', 'exclude_replies', 'contributor_details', 'include_entities'),
		'userTimeline' => array('user_id', 'screen_name', 'since_id', 'max_id', 'count', 'trim_user', 'exclude_replies', 'contributor_details', 'include_rts'),
		'mentionsTimeline' => array('count', 'since_id', 'max_id', 'trim_user', 'contributor_details', 'include_entities'),
		'retweetsOfMe' => array('since_id', 'max_id', 'count', 'trim_user', 'include_entities', 'include_user_entities'),
		'show' => array('id', 'trim_user', 'include_my_retweet', 'include_entities'),
		'retweets' => array('id', 'count', 'trim_user'),
		'oembed' => array('id', 'url', 'maxwidth', 'hide_media', 'hide_thread', 'omit_script', 'align', 'related', 'lang'),
	);

/**
 * Statuses API max number of count
 *
 * @var int
 */
	public $maxCount = 200;

/**
 * The vast majority of the custom find types actually follow the same format
 * so there was little point explicitly writing them all out. Instead, if the
 * method corresponding to the custom find type doesn't exist, the options are
 * applied to the model's request property here and then we just call
 * parent::find('all') to actually trigger the request and return the response
 * from the API.
 *
 * In addition, if you try to fetch a timeline that supports paging, but you
 * don't specify paging params, you really want all tweets in that timeline
 * since time imemoriam. But twitter will only return a maximum of 200 per
 * request. So, we make multiple calls to the API for 200 tweets at a go, for
 * subsequent pages, then merge the results together before returning them.
 *
 * Twitter's API uses a count parameter where in CakePHP we'd normally use
 * limit, so we also copy the limit value to count so we can use our familiar
 * params.
 *
 * @param string $type
 * @param array $options
 * @return mixed
 * @throws RuntimeException
 */
	public function find($type = 'first', $options = array()) {
		// change find type api 1.0 -> 1.1
		if ($type === 'mentions') {
			$type = 'mentionsTimeline';
		}

		if (in_array('count', $this->allowedFindOptions[$type])) {
			$defaults = array('count' => $this->maxCount, 'strict' => false);
			$options = array_merge($defaults, $options);

			if (!empty($options['limit']) && $options['limit'] <= $this->maxCount) {
				$options['count'] = $options['limit'];
			}
		}

		if (empty($options['page'])
			&& array_key_exists($type, $this->allowedFindOptions)
			&& in_array('count', $this->allowedFindOptions[$type])) {
			$options['page'] = 1;
			$results = array();
			try {
				while (($page = $this->find($type, $options)) != false) {
					$results = array_merge($results, $page);
					if (count($page) < $options['count']) {
						break;
					}
					if (!empty($options['limit']) && count($results) >= $options['limit']) {
						$results = array_slice($results, 0, $options['limit']);
						break;
					}
					// get next page
					if (isset($this->response['next_page'])) {
						parse_str(parse_url($this->response['next_page'], PHP_URL_QUERY), $nextPage);
						$options = am($options, $nextPage);
					} elseif (in_array('since_id', $this->allowedFindOptions[$type]) && !empty($options['since_id'])) {
						if (PHP_INT_SIZE === 4 && extension_loaded('bcmath')) {
							$options['since_id'] = bcadd($page[0]['id_str'], '1'); // for 32bit
						} else {
							$options['since_id'] = $page[0]['id'] + 1;
						}
					} elseif (in_array('max_id', $this->allowedFindOptions[$type])) {
						if (PHP_INT_SIZE === 4 && extension_loaded('bcmath')) {
							$options['max_id'] = bcsub($page[count($page) - 1]['id_str'], '1'); // for 32bit
						} else {
							$options['max_id'] = $page[count($page) - 1]['id'] - 1;
						}
					}
					// adjust count
					if (!empty($options['limit']) && $options['limit'] < count($results) + $options['count']) {
						$options['count'] = $options['limit'] - count($results);
					}
				}
			} catch (RuntimeException $e) {
				if ($options['strict']) {
					throw $e;
				}
				$this->log($e->getMessage(), LOG_DEBUG);
			}
			return $results;
		}

		if (method_exists($this, '_find' . Inflector::camelize($type))) {
			return parent::find($type, $options);
		}

		$this->_setupRequest($type, $options);

		return parent::find('all', $options);
	}

/**
 * show
 * -------------
 *
 *     TwitterStatus::find('show', $options)
 *
 * @param $state string 'before' or 'after'
 * @param $query array
 * @param $results array
 * @return mixed
 * @access protected
 * */
	protected function _findShow($state, $query = array(), $results = array()) {
		if ($state === 'before') {
			if (empty($query['id'])) {
				return $query;
			}

			$type = self::FINDTYPE_SHOW;
			$this->_setupRequest($type, $query);

			$this->request['uri']['path'] = $this->apiUrlBase . $type . '/' . $query['id'];
			unset($this->request['uri']['query']['id']);
			unset($query['id']);

			return $query;
		} else {
			return $results;
		}
	}

/**
 * retweets
 * -------------
 *
 *     TwitterStatus::find('retweets', $options)
 *
 * @param $state string 'before' or 'after'
 * @param $query array
 * @param $results array
 * @return mixed
 * @access protected
 * */
	protected function _findRetweets($state, $query = array(), $results = array()) {
		if ($state === 'before') {
			if (empty($query['id'])) {
				return $query;
			}

			$type = self::FINDTYPE_RETWEETS;

			if ($query['count'] > 100) {
				$query['count'] = 100;
			}
			$this->_setupRequest($type, $query);

			$this->request['uri']['path'] = $this->apiUrlBase . $type . '/' . $query['id'];
			unset($this->request['uri']['query']['id']);
			unset($query['id']);
			return $query;
		} else {
			return $results;
		}
	}

/**
 * Creates a tweet
 *
 * @param mixed $data
 * @param mixed $validate
 * @param mixed $fieldList
 * @return mixed
 */
	public function tweet($data = null, $validate = true, $fieldList = array()) {
		$this->request = array(
			'uri' => array(
				'path' => $this->apiUrlBase . 'update',
			),
			'method' => 'POST',
		);

		if (is_string($data)) {
			$data = array('text' => $data);
		} else if (isset($data[$this->alias])) {
			$data = $data[$this->alias];
		}

		if (isset($data['text'])) {
			$data['status'] = $data['text'];
			unset($data['text']);
		}

		$this->request['body'] = $data;

		return $this->save($data, $validate, $fieldList);
	}

/**
 * Retweets a tweet
 *
 * @param integer $id Id of the tweet you want to retweet
 * @return mixed
 */
	public function retweet($id = null) {
		if (!$id) {
			return false;
		}
		if (!is_numeric($id)) {
			return false;
		}
		$this->request = array(
			'uri' => array(
				'path' => $this->apiUrlBase . 'retweet/' . $id,
			),
		);
		$this->create();
		// Dummy data ensures Model::save() does in fact call DataSource::create()
		$data = array($this->alias => array('status' => 'dummy'));
		return $this->save($data);
	}

/**
 * Called by tweet or retweet
 *
 * @param mixed $data
 * @param mixed $validate
 * @param mixed $fieldList
 * @return mixed
 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		$this->request['auth'] = true;
		$result = parent::save($data, $validate, $fieldList);
		if ($result && !empty($this->response['id_str'])) {
			$this->setInsertID($this->response['id_str']);
		}
		return $result;
	}

/**
 * Deletes a tweet
 *
 * @param integer $id Id of the tweet to be deleted
 * @param boolean $cascade
 * @return boolean
 */
	public function delete($id = null, $cascade = true) {
		$this->request = array(
			'uri' => array(
				'path' => $this->apiUrlBase . 'destroy/' . $id,
			),
			'method' => 'POST',
			'auth' => true,
		);
		return parent::delete($id, $cascade);
	}

/**
 * Returns true if a status with the currently set ID exists.
 *
 * @return boolean True if such a status exists
 * @access public
 */
	public function exists($id = null) {
		if ($this->getID() === false) {
			return false;
		}
		$_request = $this->request;
		$result = $this->find('show', array('id' => $this->getID()));
		$this->request = $_request;
		return !empty($result);
	}

}
