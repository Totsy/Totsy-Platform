<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * This file creates a default cache configuration using the most optimized adapter available, and
 * uses it to provide default caching for high-overhead operations.
 */
use lithium\storage\Cache;
use lithium\core\Libraries;
use lithium\action\Dispatcher;
use lithium\storage\cache\adapter\XCache;
use lithium\core\Environment;

if (PHP_SAPI === 'cli') {
	return;
}

/**
 * If xcache is not available, bail out.
 */
if (!$xcacheEnabled = xcache::enabled()) {
	return;
}

Cache::config(array(
	'default' => array(
		'adapter' => 'lithium\storage\cache\adapter\XCache'
	)
));

/**
 * Caches paths for auto-loaded and service-located classes.
 */
Dispatcher::applyFilter('run', function($self, $params, $chain) {
	if (!Environment::get('production')) {
		return $chain->next($self, $params, $chain);
	}
	$key = md5(LITHIUM_APP_PATH) . '.core.libraries';

	if ($cache = Cache::read('default', $key)) {
		$cache = (array) $cache + Libraries::cache();
		Libraries::cache($cache);
	}
	$result = $chain->next($self, $params, $chain);

	if ($cache != Libraries::cache()) {
		Cache::write('default', $key, Libraries::cache(), '+1 day');
	}
	return $result;
});

?>