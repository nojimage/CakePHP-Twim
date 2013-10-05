# Twim - Twitter Plugin for CakePHP

This plugin provides DataSource and Models for Twitter API.  
In addition, provides Controller/Component for Auth, Helper and Behaviors.

for CakePHP 2.x and PHP 5.3 later

[![Build Status](https://travis-ci.org/nojimage/CakePHP-Twim.png?branch=master)](https://travis-ci.org/nojimage/CakePHP-Twim)

## Requirements

This plugin require CakePHP-ReST-DataSource-Plugin for CakePHP 2.x.

- [nojimage/CakePHP-ReST-DataSource-Plugin](https://github.com/nojimage/CakePHP-ReST-DataSource-Plugin "nojimage/CakePHP-ReST-DataSource-Plugin · GitHub")

You should install ReST DataSource plugin, before this plugin install.

## Installation

### Get plugin (git submodule)

    git submodule add https://github.com/nojimage/CakePHP-Twim.git app/Plugin/Twim

and move to plugin directory, install twitter-text-php.

    cd app/Plugin/Twim
    git submodule update --init

### Setup APP/Config/bootstrap.php

    CakePlugin::load('Twim', array('bootstrap' => true));

### Setup APP/Config/database.php

    /**
     * TwitterSource using OAuth
     *
     * @var array
     */
	public $twitter = array(
	    'datasource' => 'Twim.TwimSource',
	    'oauth_consumer_key'    => 'YOUR_CONSUMER_KEY',
	    'oauth_consumer_secret' => 'YOUR_CONSUMER_SECRET',
	    'oauth_token'           => 'YOUR_ACCESS_TOKEN',        // optional
		'oauth_token_secret'    => 'YOUR_ACCESS_TOKEN_SECRET', // optional
	    'oauth_callback'        => 'PATH_TO_OAUTH_CALLBACK_URL',
	);

## Usage

[ドキュメント(日本語)](https://github.com/nojimage/CakePHP-Twim/wiki "Home · nojimage/CakePHP-Twim Wiki")

and Please see test cases ;)

## License

The MIT License

Copyright (c) 2013 nojimage, [http://php-tips.com](http://php-tips.com)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.