<?php

/**
 * for Friendships API
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
 * @link      https://dev.twitter.com/docs/api/1.1/get/friendships/incoming
 * @link      https://dev.twitter.com/docs/api/1.1/get/friendships/outgoing
 * @link      https://dev.twitter.com/docs/api/1.1/get/friendships/show
 * @link      https://dev.twitter.com/docs/api/1.1/get/friendships/lookup
 * @link      https://dev.twitter.com/docs/api/1.1/post/friendships/create
 * @link      https://dev.twitter.com/docs/api/1.1/post/friendships/destroy
 * @link      https://dev.twitter.com/docs/api/1.1/post/friendships/update
 *
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 *
 */
class TwimFriendship extends TwimAppModel {

	public $apiUrlBase = '1.1/friendships/';

/**
 * Custom find type name
 */
	const FINDTYPE_INCOMING = 'incoming';

	const FINDTYPE_OUTGOING = 'outgoing';

	const FINDTYPE_LOOKUP = 'lookup';

	const FINDTYPE_SHOW = 'show';

/**
 * The model's schema. Used by FormHelper
 *
 * @var array
 */
	public $_schema = array(
		'id' => array('type' => 'integer', 'length' => '11'),
		'user_id' => array('type' => 'integer', 'length' => '11'),
		'screen_name' => array('type' => 'string', 'length' => '255'),
		'follow' => array('type' => 'integer', 'length' => '4'),
	);

/**
 * Validation rules for the model
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
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
	public $actsAs = array();

/**
 * Custom find types available on this model
 *
 * @var array
 */
	public $findMethods = array(
		'incoming' => true,
		'outgoing' => true,
		'show' => true,
		'lookup' => true,
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'incoming' => array('cursor', 'stringify_ids'),
		'outgoing' => array('cursor', 'stringify_ids'),
		'show' => array('source_id', 'target_id', 'source_screen_name', 'target_screen_name'),
		'lookup' => array('screen_name', 'user_id'),
	);

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
	public function find($type = 'first', $options = array()) {
		if (!empty($options['limit']) && empty($options['count'])) {
			$options['count'] = $options['limit'];
		}
		if (method_exists($this, '_find' . Inflector::camelize($type))) {
			return parent::find($type, $options);
		}

		$this->_setupRequest($type, $options);

		return parent::find('all', $options);
	}

/**
 * Allows the authenticating users to follow the user specified in the ID parameter.
 *
 * @param mixed $data
 * @param mixed $validate
 * @return mixed
 */
	public function create($data = null, $validate = true) {
		$this->request = array(
			'uri' => array(
				'path' => '1.1/friendships/create',
			),
			'method' => 'POST',
		);

		if (is_numeric($data)) {
			$data = array('user_id' => $data);
		} else if (is_string($data)) {
			$data = array('screen_name' => $data);
		} else if (isset($data[$this->alias])) {
			$data = $data[$this->alias];
		}

		$this->request['body'] = $data;

		return $this->save($data, $validate);
	}

/**
 * Allows one to enable or disable retweets and device notifications from the specified user.
 *
 * @param mixed $data
 * @param mixed $validate
 * @return mixed
 */
	public function update($data = null, $validate = true) {
		$this->request = array(
			'uri' => array(
				'path' => '1.1/friendships/update',
			),
			'method' => 'POST',
		);

		if (isset($data[$this->alias])) {
			$data = $data[$this->alias];
		}

		$this->request['body'] = $data;

		return $this->save($data, $validate);
	}

/**
 * Called by create or update
 *
 * @param mixed $data
 * @param mixed $validate
 * @param mixed $fieldList
 * @return mixed
 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		$this->request['auth'] = true;
		$result = $this->getDataSource()->create($this);
		if ($result && !empty($this->response['user']['id_str'])) {
			$this->setInsertID($this->response['user']['id_str']);
		}
		return $result;
	}

/**
 * alias of delete
 *
 * @param mixed $id user_id or screen_name or parameter array
 * @return array
 */
	public function destroy($id) {
		return $this->delete($id);
	}

/**
 * Deletes a firendship
 *
 * @param mixed $id user_id or screen_name or parameter array
 * @param boolean $cascade
 * @return array
 */
	public function delete($id = null, $cascade = true) {
		$this->request = array(
			'uri' => array(
				'path' => '1.1/friendships/destroy',
			),
			'method' => 'POST',
			'auth' => true,
		);

		if (is_numeric($id)) {
			$id = array('user_id' => $id);
		} else if (is_string($id)) {
			$id = array('screen_name' => $id);
		}
		$this->request['body'] = $id;

		return $this->getDataSource()->delete($this);
	}

/**
 * Test for the existence of friendship between two users. Will return true if user_a follows user_b, otherwise will return false.
 *
 * @param  string $source user_id or screen_name
 * @param  string $target user_id or screen_name
 * @return boolean True if such source follows target
 * @access public
 * @deprecated since version 2.1.0
 */
	public function exists($id = null) {
		list($source, $target) = func_get_args() + array(null, null);
		if (is_null($target)) {
			if ($this->getID() === false) {
				return false;
			}
			$target = $source;
			$source = $this->getID();
		}

		$_request = $this->request;

		$result = false;
		$params = array();

		if (!is_numeric($source) && !is_numeric($target)) {
			$params = array('source_screen_name' => $source, 'target_screen_name' => $target);
		} else {
			$params = array('source_id' => $source, 'target_id' => $target);
		}
		$response = $this->find('show', $params);

		$this->exists_request = $this->request;
		$this->request = $_request;

		if (isset($response['relationship']['source']['following'])) {
			$result = $response['relationship']['source']['following'];
		}
		return $result;
	}

}
