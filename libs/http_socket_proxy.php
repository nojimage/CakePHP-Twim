<?php

App::import('Core', 'HttpSocket');

/**
 * HttpSocket support Proxy
 *
 * for CakePHP 1.3+
 * PHP version 5.2+
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
 *
 */
class HttpSocketProxy extends HttpSocket {

	/**
	 * Proxy settings
	 *
	 * @var array
	 */
	protected $_proxy = array();

	function request($request = array()) {
		$this->reset(false);

		if (is_string($request)) {
			$request = array('uri' => $request);
		} elseif (!is_array($request)) {
			return false;
		}

		if (!isset($request['uri'])) {
			$request['uri'] = null;
		}
		$uri = $this->_parseUri($request['uri']);
		$hadAuth = false;
		if (is_array($uri) && array_key_exists('user', $uri)) {
			$hadAuth = true;
		}
		if (!isset($uri['host'])) {
			$host = $this->config['host'];
		}
		if (isset($request['host'])) {
			$host = $request['host'];
			unset($request['host']);
		}
		$request['uri'] = $this->url($request['uri']);
		$request['uri'] = $this->_parseUri($request['uri'], true);
		$this->request = Set::merge($this->request, $this->config['request'], $request);

		if (!$hadAuth && !empty($this->config['request']['auth']['user'])) {
			$this->request['uri']['user'] = $this->config['request']['auth']['user'];
			$this->request['uri']['pass'] = $this->config['request']['auth']['pass'];
		}
		$this->_configUri($this->request['uri']);

		if (isset($host)) {
			$this->config['host'] = $host;
		}
		$this->_setProxy();
		$this->request['proxy'] = $this->_proxy;

		$cookies = null;

		if (is_array($this->request['header'])) {
			$this->request['header'] = $this->_parseHeader($this->request['header']);
			if (!empty($this->request['cookies'])) {
				$cookies = $this->buildCookies($this->request['cookies']);
			}
			$Host = $this->request['uri']['host'];
			$scheme = '';
			$port = 0;
			if (isset($this->request['uri']['scheme'])) {
				$scheme = $this->request['uri']['scheme'];
			}
			if (isset($this->request['uri']['port'])) {
				$port = $this->request['uri']['port'];
			}
			if (
				($scheme === 'http' && $port != 80) ||
				($scheme === 'https' && $port != 443) ||
				($port != 80 && $port != 443)
			) {
				$Host .= ':' . $port;
			}
			$this->request['header'] = array_merge(compact('Host'), $this->request['header']);
		}

		if (isset($this->request['auth']['user']) && isset($this->request['auth']['pass'])) {
			$this->request['header']['Authorization'] = $this->request['auth']['method'] . " " . base64_encode($this->request['auth']['user'] . ":" . $this->request['auth']['pass']);
		}
		if (isset($this->request['uri']['user']) && isset($this->request['uri']['pass'])) {
			$this->request['header']['Authorization'] = $this->request['auth']['method'] . " " . base64_encode($this->request['uri']['user'] . ":" . $this->request['uri']['pass']);
		}

		if (is_array($this->request['body'])) {
			$this->request['body'] = $this->_httpSerialize($this->request['body']);
		}

		if (!empty($this->request['body']) && !isset($this->request['header']['Content-Type'])) {
			$this->request['header']['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		if (!empty($this->request['body']) && !isset($this->request['header']['Content-Length'])) {
			$this->request['header']['Content-Length'] = strlen($this->request['body']);
		}

		$connectionType = null;
		if (isset($this->request['header']['Connection'])) {
			$connectionType = $this->request['header']['Connection'];
		}
		$this->request['header'] = $this->_buildHeader($this->request['header']) . $cookies;

		if (empty($this->request['line'])) {
			$this->request['line'] = $this->_buildRequestLine($this->request);
		}

		if ($this->quirksMode === false && $this->request['line'] === false) {
			return $this->response = false;
		}

		if ($this->request['line'] !== false) {
			$this->request['raw'] = $this->request['line'];
		}

		if ($this->request['header'] !== false) {
			$this->request['raw'] .= $this->request['header'];
		}

		$this->request['raw'] .= "\r\n";
		$this->request['raw'] .= $this->request['body'];
		$this->write($this->request['raw']);

		$response = null;
		while ($data = $this->read()) {
			$response .= $data;
		}

		if ($connectionType == 'close') {
			$this->disconnect();
		}

		$this->response = $this->_parseResponse($response);
		if (!empty($this->response['cookies'])) {
			$this->config['request']['cookies'] = array_merge($this->config['request']['cookies'], $this->response['cookies']);
		}

		// fixes content type
		if (isset($this->response['header']['Content-Type']) && is_array($this->response['header']['Content-Type'])) {
			$contentType = $this->response['header']['Content-Type'];
			$this->response['header']['Content-Type'] = $contentType[count($contentType) - 1];
		}

		return $this->response['body'];
	}

	function _buildRequestLine($request = array(), $versionToken = 'HTTP/1.1') {
		$asteriskMethods = array('OPTIONS');

		if (is_string($request)) {
			$isValid = preg_match("/(.+) (.+) (.+)\r\n/U", $request, $match);
			if (!$this->quirksMode && (!$isValid || ($match[2] == '*' && !in_array($match[3], $asteriskMethods)))) {
				trigger_error(__('HttpSocket::_buildRequestLine - Passed an invalid request line string. Activate quirks mode to do this.', true), E_USER_WARNING);
				return false;
			}
			return $request;
		} elseif (!is_array($request)) {
			return false;
		} elseif (!array_key_exists('uri', $request)) {
			return false;
		}

		$request['uri'] = $this->_parseUri($request['uri']);
		$request = array_merge(array('method' => 'GET'), $request);
		if (!empty($this->_proxy['host'])) {
			$request['uri'] = $this->_buildUri($request['uri'], '%scheme://%host:%port/%path?%query');
		} else {
			$request['uri'] = $this->_buildUri($request['uri'], '/%path?%query');
		}

		if (!$this->quirksMode && $request['uri'] === '*' && !in_array($request['method'], $asteriskMethods)) {
			trigger_error(sprintf(__('HttpSocket::_buildRequestLine - The "*" asterisk character is only allowed for the following methods: %s. Activate quirks mode to work outside of HTTP/1.1 specs.', true), join(',', $asteriskMethods)), E_USER_WARNING);
			return false;
		}
		return $request['method'] . ' ' . $request['uri'] . ' ' . $versionToken . $this->lineBreak;
	}

	/**
	 * Set proxy settings
	 *
	 * @param mixed $host Proxy host. Can be an array with settings to authentication class
	 * @param integer $port Port. Default 3128.
	 * @param string $method Proxy method (ie, Basic, Digest). If empty, disable proxy authentication
	 * @param string $user Username if your proxy need authentication
	 * @param string $pass Password to proxy authentication
	 * @return void
	 */
	public function configProxy($host, $port = 3128, $method = null, $user = null, $pass = null) {
		if (empty($host)) {
			$this->_proxy = array();
			return;
		}
		if (is_array($host)) {
			$this->_proxy = $host + array('host' => null);
			return;
		}
		$this->_proxy = compact('host', 'port', 'method', 'user', 'pass');
	}

	/**
	 * Set the proxy configuration and authentication
	 *
	 * @return void
	 * @throws SocketException
	 */
	protected function _setProxy() {
		if (empty($this->_proxy) || !isset($this->_proxy['host'], $this->_proxy['port'])) {
			return;
		}
		$this->config['host'] = $this->_proxy['host'];
		$this->config['port'] = $this->_proxy['port'];

		if (empty($this->_proxy['method']) || !isset($this->_proxy['user'], $this->_proxy['pass'])) {
			return;
		}

		$this->proxyAuthentication($this, $this->_proxy);
	}

	/**
	 * Proxy Authentication
	 *
	 * @param HttpSocket $http
	 * @param array $proxyInfo
	 * @return void
	 * @link http://www.ietf.org/rfc/rfc2617.txt
	 */
	public static function proxyAuthentication(HttpSocket $http, &$proxyInfo) {
		if (isset($proxyInfo['user'], $proxyInfo['pass'])) {
			$http->request['header']['Proxy-Authorization'] = self::_generateHeader($proxyInfo['user'], $proxyInfo['pass']);
		}
	}

	/**
	 * Generate basic [proxy] authentication header
	 *
	 * @param string $user
	 * @param string $pass
	 * @return string
	 */
	protected static function _generateHeader($user, $pass) {
		return 'Basic ' . base64_encode($user . ':' . $pass);
	}

}
