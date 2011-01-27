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

/**
 * If xcache is not available, bail out.
 */
if (!$xcacheEnabled = xcache::enabled()) {
	return;
}

Cache::config(array(
	'default' => array(
		'adapter' => '\lithium\storage\cache\adapter\XCache'
	)
));

Dispatcher::applyFilter('run', function($self, $params, $chain) {
	$key = md5(LITHIUM_APP_PATH);

	if ($cache = Cache::read('default', "{$key}.admin.core.libraries")) {
		$cache = (array) unserialize($cache) + Libraries::cache();
		Libraries::cache($cache);
	}
	$result = $chain->next($self, $params, $chain);

	if ($cache != Libraries::cache()) {
		Cache::write('default', "{$key}.admin.core.libraries", serialize(Libraries::cache()), '+1 day');
	}
	return $result;
});

?>
