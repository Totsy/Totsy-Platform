<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

require 'webroot/index.php';

function d($a) {
	ob_start();
	var_dump($a);
	$dump = ob_get_clean();
	Logger::debug($dump);
}

?>