<?php

App::import('Model', 'Twitter.TwitterAppModel');

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
 *
 * @property TwitterSource $Source
 */
class TwimAppModel extends TwitterAppModel {

    public function __get($name) {
        return ClassRegistry::init('Twim.Twim' . $name);
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     *
     * @return TwitterSource
     */
    public function getDataSource() {
        return ConnectionManager::getDataSource($this->useDbConfig);
    }

    public function setDataSource($name) {
        $this->useDbConfig = $name;
        return $this;
    }

    public function setDataSourceConfig($config = array()) {
        parent::setDataSourceConfig($config);
        return $this;
    }

    public function onError() {

        parent::onError();

        // == throw Expection
        $message = $this->getDataSource()->Http->response['body'];

        if (!empty($this->response['error'])) {
            $message = $this->response['error'];
        }

        throw new RuntimeException(
                $message,
                $this->getDataSource()->Http->response['status']['code']);
    }

}
