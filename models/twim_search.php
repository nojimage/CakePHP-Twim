<?php

/**
 * for Search API
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
 * @link    　http://php-tips.com/
 * @since   　File available since Release 1.0
 * @see http://dev.twitter.com/doc/get/search
 *
 * NOTICE: Search API "from_user_id" doesn't match up with the proper Twitter "user_id"
 * see http://code.google.com/p/twitter-api/issues/detail?id=214
 *
 */
class TwimSearch extends TwimAppModel {

    /**
     * Custom find types available on this model
     *
     * @var array
     */
    public $_findMethods = array(
        'search' => true,
    );
    /**
     * The options allowed by each of the custom find types
     *
     * @var array
     */
    public $allowedFindOptions = array(
        'search' => array('lang', 'locale', 'max_id', 'q', 'rpp', 'page', 'since', 'since_id', 'geocode', 'show_user', 'until', 'result_type'),
    );

    public function find($type, $options = array()) {

        if (is_string($type) && empty($options)) {
            $options = $type;
            $type = 'search';
        }

        if (is_string($options)) {
            $q = $options;
            $options = compact('q');
        }

        if (!empty($options['limit']) && empty($options['rpp'])) {
            $options['rpp'] = $options['limit'];
        }

        if (empty($options['page']) && empty($options['limit'])) {
            $options['page'] = 1;
            $options['limit'] = 200;
            $results = array();
            try {
                while (($page = $this->find($type, $options)) != false) {
                    $results = array_merge($results, $page);
                    $options['page']++;
                }
            } catch (Exception $e) {

            }
            return $results;
        }

        $this->request['uri']['host'] = 'search.twitter.com';
        $this->request['uri']['path'] = 'search';

        if (array_key_exists($type, $this->allowedFindOptions)) {
            $this->request['uri']['query'] = array_intersect_key($options, array_flip($this->allowedFindOptions[$type]));
        }

        return Set::extract('/results/.', parent::find('all', $options));
    }

}
