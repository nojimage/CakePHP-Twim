<?php

/**
 * for Account API
 *
 * PHP versions 5
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   1.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2011 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   twim
 * @since   　File available since Release 1.0
 * @link      https://dev.twitter.com/docs/api/1/get/account/rate_limit_status
 * @link      https://dev.twitter.com/docs/api/1/get/account/verify_credentials
 * @link      https://dev.twitter.com/docs/api/1/post/account/end_session
 * @link      https://dev.twitter.com/docs/api/1/post/account/update_delivery_device
 * @link      https://dev.twitter.com/docs/api/1/post/account/update_profile
 * @link      https://dev.twitter.com/docs/api/1/post/account/update_profile_background_image
 * @link      https://dev.twitter.com/docs/api/1/post/account/update_profile_colors
 * @link      https://dev.twitter.com/docs/api/1/post/account/update_profile_image
 * @link      https://dev.twitter.com/docs/api/1/get/account/totals
 * @link      https://dev.twitter.com/docs/api/1/get/account/settings
 * @link      https://dev.twitter.com/docs/api/1/post/account/settings
 * @todo support post methods
 * @todo find method (verifyCredentials. totals, settings) not test yet.
 */
class TwimAccount extends TwimAppModel {

    public $apiUrlBase = '1/account/';

    /**
     * Custom find types available on this model
     *
     * @var array
     */
    public $_findMethods = array(
        'rateLimitStatus' => true,
        'verifyCredentials' => true,
        'totals' => true,
        'settings' => true,
    );

    /**
     * The custom find types that require authentication
     *
     * @var array
     */
    public $findMethodsRequiringAuth = array(
        'verifyCredentials',
        'totals',
        'settings',
    );

    /**
     * The options allowed by each of the custom find types
     * 
     * @var array
     */
    public $allowedFindOptions = array(
        'rateLimitStatus' => array(),
        'verifyCredentials' => array('include_entities', 'skip_status'),
        'totals' => array(),
        'settings' => array(),
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
    public function find($type, $options = array()) {

        if (method_exists($this, '_find' . Inflector::camelize($type))) {
            return parent::find($type, $options);
        }

        $this->_setupRequest($type, $options);

        return parent::find('all', $options);
    }

}