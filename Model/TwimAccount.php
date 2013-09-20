<?php

/**
 * for Account API
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
 * @link      https://dev.twitter.com/docs/api/1.1/get/account/settings
 * @link      https://dev.twitter.com/docs/api/1.1/get/account/verify_credentials
 * @todo support post methods
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 *
 */
class TwimAccount extends TwimAppModel {

	public $apiUrlBase = '1.1/account/';

/**
 * Custom find type name
 */
	const FINDTYPE_SETTINGS = 'settings';

	const FINDTYPE_VERIFY_CREDENTIALS = 'verifyCredentials';

/**
 * Custom find types available on this model
 *
 * @var array
 */
	public $findMethods = array(
		'settings' => true,
		'verifyCredentials' => true,
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'settings' => array(),
		'verifyCredentials' => array('include_entities', 'skip_status'),
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
		if (method_exists($this, '_find' . Inflector::camelize($type))) {
			return parent::find($type, $options);
		}

		$this->_setupRequest($type, $options);

		return parent::find('all', $options);
	}

}
