<?php

/**
 * for Status API
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
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/home_timeline
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/user_timeline
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/mentions
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/retweeted_by_me
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/retweeted_to_me
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/retweets_of_me
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/show/:id
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/retweets/:id
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/:id/retweeted_by
 * @link      https://dev.twitter.com/docs/api/1/get/statuses/:id/retweeted_by/ids
 * @link      https://dev.twitter.com/docs/api/1/post/statuses/update
 * @link      https://dev.twitter.com/docs/api/1/post/statuses/retweet/:id
 * @link      https://dev.twitter.com/docs/api/1/post/statuses/destroy/:id
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

	public $apiUrlBase = '1/statuses/';

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
		'retweetedByMe' => true,
		'retweetedToMe' => true,
		'show' => true,
		'retweetsOfMe' => true,
		'retweets' => true,
		'retweetedBy' => true,
		'retweetedByIds' => true,
	);

/**
 * The custom find types that require authentication
 *
 * @var array
 */
	public $findMethodsRequiringAuth = array(
		'homeTimeline',
		'userTimeline',
		'mentions',
		'retweetedByMe',
		'retweetedToMe',
		'retweetsOfMe',
		'retweets',
		'retweetedBy',
		'retweetedByIds',
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'homeTimeline' => array('since_id', 'max_id', 'count', 'page', 'trim_user', 'include_entities'),
		'userTimeline' => array('user_id', 'screen_name', 'since_id', 'max_id', 'count', 'page', 'trim_user', 'include_rts', 'include_entities'),
		'mentions' => array('since_id', 'max_id', 'count', 'page', 'trim_user', 'include_rts', 'include_entities'),
		'retweetedByMe' => array('since_id', 'max_id', 'count', 'page', 'trim_user', 'include_entities'),
		'retweetedToMe' => array('since_id', 'max_id', 'count', 'page', 'trim_user', 'include_entities'),
		'retweetsOfMe' => array('since_id', 'max_id', 'count', 'page', 'trim_user', 'include_entities'),
		'show' => array('id', 'trim_user', 'include_entities'),
		'retweets' => array('id', 'count', 'trim_user', 'include_entities'),
		'retweetedBy' => array('id', 'count', 'page', 'trim_user', 'include_entities'),
		'retweetedByIds' => array('id', 'count', 'page', 'trim_user', 'include_entities'),
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
 */
	public function find($type, $options = array()) {
		if (in_array('count', $this->allowedFindOptions[$type])) {
			$defaults = array('count' => $this->maxCount, 'strict' => false);
			$options = array_merge($defaults, $options);

			if (!empty($options['limit']) && $options['limit'] <= $this->maxCount) {
				$options['count'] = $options['limit'];
			}
		}

		if (empty($options['page'])
			&& array_key_exists($type, $this->allowedFindOptions)
			&& in_array('page', $this->allowedFindOptions[$type])
			&& in_array('count', $this->allowedFindOptions[$type])) {
			$options['page'] = 1;
			$results = array();
			try {
				while (($page = $this->find($type, $options)) != false) {
					$results = array_merge($results, $page);
					if (!empty($options['limit']) && count($results) >= $options['limit']) {
						$results = array_slice($results, 0, $options['limit']);
						break;
					}
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
					} else {
						$options['page']++;
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

			$type = 'show';

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

			$type = 'retweets';

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
 * Retweeted By
 * -------------
 *
 *     TwitterStatus::find('retweetedBy', $options)
 *
 * @param $state string 'before' or 'after'
 * @param $query array
 * @param $results array
 * @return mixed
 * @access protected
 * */
	protected function _findRetweetedBy($state, $query = array(), $results = array()) {
		if ($state === 'before') {
			if (empty($query['id'])) {
				return $query;
			}

			$type = 'retweetedBy';

			if ($query['count'] > 100) {
				$query['count'] = 100;
			}
			$this->_setupRequest($type, $query);

			$this->request['uri']['path'] = $this->apiUrlBase . $query['id'] . '/retweeted_by';
			unset($this->request['uri']['query']['id']);
			unset($query['id']);

			return $query;
		} else {
			return $results;
		}
	}

/**
 * Retweeted By Ids
 * -------------
 *
 *     TwitterStatus::find('retweetedByIds', $options)
 *
 * @param $state string 'before' or 'after'
 * @param $query array
 * @param $results array
 * @return mixed
 * @access protected
 * */
	protected function _findRetweetedByIds($state, $query = array(), $results = array()) {
		if ($state === 'before') {
			if (empty($query['id'])) {
				return $query;
			}

			$type = 'retweetedByIds';

			if ($query['count'] > 100) {
				$query['count'] = 100;
			}
			$this->_setupRequest($type, $query);

			$this->request['uri']['path'] = $this->apiUrlBase . $query['id'] . '/retweeted_by/ids';
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
				'path' => '1/statuses/update',
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
				'path' => '1/statuses/retweet/' . $id,
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
				'path' => '1/statuses/destroy/' . $id,
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
	public function exists() {
		if ($this->getID() === false) {
			return false;
		}
		$_request = $this->request;
		$result = $this->find('show', array('id' => $this->getID()));
		$this->request = $_request;
		return !empty($result);
	}

}
