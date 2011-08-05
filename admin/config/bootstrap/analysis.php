<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\analysis\Logger;

Logger::config(array(
    'default' => array('adapter' => 'File'),
));

function d($a) {
	ob_start();
	var_dump($a);
	$dump = ob_get_clean();
	Logger::debug($dump);
}

?>