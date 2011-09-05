<?php

/**
 * Twim Base Model
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
 * @original  https://github.com/neilcrookes/CakePHP-Twitter-API-Plugin
 **/
/*
 * @property TwimSource $Source
 */
class TwimAppModel extends AppModel {

    /**
     * The datasource all models in the plugin use.
     *
     * @var string
     */
    public $useDbConfig = 'twitter';

    /**
     * The models in the plugin get data from the web service, so they don't need
     * a table.
     *
     * @var string
     */
    public $useTable = false;

    /**
     * Methods in the models result in HTTP requests using the HttpSocket. So
     * rather than do all the heavy lifting in the datasource, we set the various
     * params of the request in the individual model methods. This ties the model
     * to the data layer, but these models are especially for this datasource.
     *
     * @var array
     */
    public $request = array();

    public function __get($name) {
        $model = ClassRegistry::init('Twim.Twim' . $name);
        return $model;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * Adds the datasource to the connection manager if it's not already there,
     * which it won't be if you've not added it to your app/config/database.php
     * file.
     *
     * @param $id
     * @param $table
     * @param $ds
     */
    public function __construct($id = false, $table = null, $ds = null) {

        $sources = ConnectionManager::sourceList();

        if (!in_array('twitter', $sources)) {
            ConnectionManager::create('twitter', array('datasource' => 'Twim.TwimSource'));
        }

        parent::__construct($id, $table, $ds);
    }

    /**
     *
     * @return TwimSource
     */
    public function getDataSource() {
        return ConnectionManager::getDataSource($this->useDbConfig);
    }

    public function setDataSource($dataSource) {
        parent::setDataSource($dataSource);
        return $this;
    }

    public function setDataSourceConfig($config = array()) {
        $ds = $this->getDataSource($this->useDbConfig);
        if (!is_array($ds->config)) {
            $ds->config = array($ds->config);
        }
        $ds->config = array_merge($ds->config, $config);
        return $this;
    }

    public function onError() {

        parent::onError();

        // == throw Expection
        if ($this->getDataSource()->config['throw_exception']) {
            $message = $this->getDataSource()->Http->response['body'];

            if (!empty($this->response['error'])) {
                $message = $this->response['error'];
            }

            throw new RuntimeException(
                    $message,
                    $this->getDataSource()->Http->response['status']['code']);
        }
    }

}
