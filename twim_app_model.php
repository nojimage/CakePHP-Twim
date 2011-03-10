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
 *
 * @property TwitterSource $Source
 */
class TwimAppModel extends Object {

    public $name;
    public $alias;
    public $useDbConfig = 'twitter';

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

    /**
     *
     * @param string $ds
     */
    public function __construct($ds = 'twitter') {

        if (is_array($ds)) {
            extract(array_merge(
                            array(
                                'ds' => $this->useDbConfig,
                                'name' => $this->name, 'alias' => $this->alias
                            ),
                            $ds
            ));
        }

        if ($this->name === null) {
            $this->name = (isset($name) ? $name : get_class($this));
        }

        if ($this->alias === null) {
            $this->alias = (isset($alias) ? $alias : $this->name);
        }

        ClassRegistry::addObject($this->alias, $this);

        $this->setDataSource($ds);
    }

}
