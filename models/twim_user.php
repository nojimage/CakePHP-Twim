<?php

/**
 * for Users API
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
 * @see       https://dev.twitter.com/docs/api/1/get/users/lookup
 * @see       https://dev.twitter.com/docs/api/1/get/users/search
 * @see       https://dev.twitter.com/docs/api/1/get/users/show
 * @see       https://dev.twitter.com/docs/api/1/get/users/contributees
 * @see       https://dev.twitter.com/docs/api/1/get/users/contributors
 * @see       https://dev.twitter.com/docs/api/1/get/users/suggestions
 * @see       https://dev.twitter.com/docs/api/1/get/users/suggestions/%3Aslug
 * @see       https://dev.twitter.com/docs/api/1/get/users/suggestions/%3Aslug/members
 *
 */
class TwimUser extends TwimAppModel {

    public $apiUrlBase = '1/users/';

    /**
     * Custom find types available on this model
     *
     * @var array
     */
    public $_findMethods = array(
        'lookup' => true,
        'search' => true,
        'show' => true,
        'contributees' => true,
        'contributors' => true,
            # TODO: support suggestions api
            # 'suggestions' => true,
            # 'suggestionsMembers' => true,
    );

    /**
     * The custom find types that require authentication
     *
     * @var array
     */
    public $findMethodsRequiringAuth = array(
        'search',
    );

    /**
     * The options allowed by each of the custom find types
     * 
     * @var array
     */
    public $allowedFindOptions = array(
        'lookup' => array('screen_name', 'user_id', 'include_entities'),
        'search' => array('q', 'page', 'per_page', 'include_entities'),
        'show' => array('user_id', 'screen_name', 'include_entities'),
        'contributees' => array('user_id', 'screen_name', 'include_entities', 'skip_status'),
        'contributors' => array('user_id', 'screen_name', 'include_entities', 'skip_status'),
        'suggestions' => array('slug', 'lang'),
        'suggestionsMembers' => array('slug'),
    );

    /**
     * max items per page for users/search
     *
     * @var int
     */
    public $maxPerPage = 20;

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

    /**
     * lookup
     * -------------
     *
     *     TwitterUser::find('lookup', $options)
     *
     * @param $state string 'before' or 'after'
     * @param $query array
     * @param $results array
     * @return mixed
     * @access protected
     * */
    protected function _findLookup($state, $query = array(), $results = array()) {
        if ($state === 'before') {

            if (empty($query['user_id']) && empty($query['screen_name'])) {
                return $query;
            }

            // flatten option
            foreach (array('user_id', 'screen_name') as $key) {
                if (isset($query[$key]) && is_array($query[$key])) {
                    $query[$key] = join(',', $query[$key]);
                }
            }

            $this->_setupRequest('lookup', $query);

            return $query;
        } else {
            return $results;
        }
    }

    /**
     * search
     * -------------
     *
     *     TwitterUser::find('search', $options)
     *
     * @param $state string 'before' or 'after'
     * @param $query array
     * @param $results array
     * @return mixed
     * @access protected
     * */
    protected function _findSearch($state, $query = array(), $results = array()) {
        if ($state === 'before') {

            if (empty($query['q'])) {
                return $query;
            }

            $type = 'search';

            if (empty($query['page'])) {
                $query['page'] = 1;
            }

            $this->_setupRequest($type, $query);
            return $query;
        } else {
            return $results;
        }
    }

}
