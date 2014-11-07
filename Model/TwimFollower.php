<?php

/**
 * for Followers API
 *
 * CakePHP 2.x
 * PHP version 5
 *
 * Copyright 2014, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   2.1
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2014 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   Twim
 * @since     File available since Release 1.0
 *
 * @link      https://dev.twitter.com/rest/reference/get/followers/ids
 * @link      https://dev.twitter.com/rest/reference/get/followers/list
 *
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 *
 */
class TwimFollower extends TwimAppModel {

	public $apiUrlBase = '1.1/followers/';

/**
 * Custom find type name
 */
	const FINDTYPE_IDS = 'ids';

	const FINDTYPE_LIST = 'list';

/**
 * Custom find types available on this model
 *
 * @var array
 */
	public $findMethods = array(
		'ids' => true,
		'list' => true,
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'ids' => array('user_id', 'screen_name', 'cursor', 'stringify_ids', 'count'),
		'list' => array('user_id', 'screen_name', 'cursor', 'count', 'skip_status', 'include_user_entities'),
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
		if ($type != 'list' && method_exists($this, '_find' . Inflector::camelize($type))) {
			return parent::find($type, $options);
		}

		$this->_setupRequest($type, $options);

		return parent::find('all', $options);
	}

}
