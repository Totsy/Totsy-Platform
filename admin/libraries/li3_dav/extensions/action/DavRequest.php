<?php

namespace li3_dav\extensions\action;

class DavRequest extends \lithium\action\Request {

	protected function _init() {
		/* Deliberately not calling parent::_init(). */

		$mobile = array(
			'iPhone', 'MIDP', 'AvantGo', 'BlackBerry', 'J2ME', 'Opera Mini', 'DoCoMo', 'NetFront',
			'Nokia', 'PalmOS', 'PalmSource', 'portalmmm', 'Plucker', 'ReqwirelessWeb', 'iPod',
			'SonyEricsson', 'Symbian', 'UP\.Browser', 'Windows CE', 'Xiino', 'Android'
		);
		if (!empty($this->_config['detectors']['mobile'][1])) {
			$mobile = array_merge($mobile, (array) $this->_config['detectors']['mobile'][1]);
		}
		$this->_detectors['mobile'][1] = $mobile;
		$this->_env += (array) $_SERVER + (array) $_ENV + array('REQUEST_METHOD' => 'GET');
		$envs = array('isapi' => 'IIS', 'cgi' => 'CGI', 'cgi-fcgi' => 'CGI');
		$this->_env['PLATFORM'] = isset($envs[PHP_SAPI]) ? $envs[PHP_SAPI] : null;
		$this->_base = isset($this->_base) ? $this->_base : $this->_base();
		$this->url = '/';

		if (isset($this->_config['url'])) {
			$this->url = rtrim($this->_config['url'], '/');
		} elseif (!empty($_GET['url']) ) {
			$this->url = rtrim($_GET['url'], '/');
			unset($_GET['url']);
		}

		if (!empty($this->_config['query'])) {
			$this->query = $this->_config['query'];
		}
		if (isset($_GET)) {
			$this->query += $_GET;
		}

		if (!empty($this->_config['data'])) {
			$this->data = $this->_config['data'];
		} elseif (isset($_POST)) {
			$this->data += $_POST;
		}

		if (isset($this->data['_method'])) {
			$this->_env['HTTP_X_HTTP_METHOD_OVERRIDE'] = strtoupper($this->data['_method']);
			unset($this->data['_method']);
		}

		if (!empty($this->_env['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
			$this->_env['REQUEST_METHOD'] = $this->_env['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}
	}

	public function stream() {
		return $this->_stream = $this->_stream ?: fopen('php://input', 'r');
	}
}

?>