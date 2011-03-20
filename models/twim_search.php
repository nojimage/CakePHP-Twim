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
 * @see http://apiwiki.twitter.com/Twitter-Search-API-Method%3A-search
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
        if (empty($options['page']) || empty($options['rpp'])) {
            $options['page'] = 1;
            $options['rpp'] = 200;
            $results = array();
            while (($page = $this->find($type, $options)) != false) {
                $results = array_merge($results, $page);
                $options['page']++;
            }
            return $results;
        }

        $this->request['uri']['host'] = 'search.twitter.com';
        $this->request['uri']['path'] = 'search';

        if (array_key_exists($type, $this->allowedFindOptions)) {
            $this->request['uri']['query'] = array_intersect_key($options, array_flip($this->allowedFindOptions[$type]));
        }
        return parent::find('all', $options);
    }

}
