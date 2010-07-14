<?php

namespace app\extensions;

use \Exception;

class ErrorHandler extends \lithium\core\ErrorHandler {

	/**
	 * Receives the handled errors and exceptions that have been caught, and processes them
	 * in a normalized manner.
	 *
	 * @param object|array $info
	 * @param array $scope
	 * @return boolean True if successfully handled, false otherwise.
	 */
	public static function handle($info, $scope = array()) {
		$rules = $scope ?: static::$_config;
		$handler = static::$_exceptionHandler;
		$info = is_object($info) ? $handler($info, true) : $info;

		$defaults = array(
			'type' => null, 'code' => 0, 'message' => null, 'file' => null, 'line' => 0,
			'trace' => array(), 'context' => null, 'exception' => null
		);
		$info = (array) $info + $defaults;

		$info['stack'] = static::_trace($info['trace']);
		$info['origin'] = static::_origin($info['trace']);

		foreach ($rules as $config) {
			if (!static::matches($info, $config)) {
				continue;
			}
			if ((isset($config['scope'])) && static::handle($info, $config['scope']) !== false) {
				return true;
			}
			if (!isset($config['handler'])) {
				return false;
			}
			return $config['handler']($info);
		}
		return false;
	}

	public static function matches($info, $conditions) {
		$checks = static::$_checks;
		$handler = static::$_exceptionHandler;
		$info = is_object($info) ? $handler($info, true) : $info;

		foreach (array_keys($conditions) as $key) {
			if ($key == 'conditions' || $key == 'scope' || $key == 'handler') {
				continue;
			}
			if (!isset($info[$key]) || !isset($checks[$key])) {
				return false;
			}
			if (($check = $checks[$key]) && !$check($conditions, $info)) {
				return false;
			}
		}
		if ((isset($config['conditions']) && $call = $config['conditions']) && !$call($info)) {
			return false;
		}
		return true;
	}

	public static function apply($class, $method, array $conditions, $handler) {
		$_self = get_called_class();

		$filter = function($self, $params, $chain) use ($_self, $conditions, $handler) {
			try {
				return $chain->next($self, $params, $chain);
			} catch (Exception $e) {
				if (!$_self::matches($e, $conditions)) {
					throw $e;
				}
				return $handler($e, $params);
			}
		};

		if (is_string($class)) {
			$class::applyFilter($method, $filter);
		} else {
			$class->applyFilter($method, $filter);
		}
	}
}

?>