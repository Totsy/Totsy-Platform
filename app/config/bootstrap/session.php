<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * This configures your session storage. The Cookie storage adapter must be connected first, since
 * it intercepts any writes where the `'expires'` key is set in the options array.
 */
use lithium\storage\Session;

Session::config(array(
	'default' => array(
		'adapter' => 'app\extensions\adapter\session\Model',
		'model' => 'totsy_common\models\MongoSession'
	),
	'cookie' => array(
		'adapter' => 'Cookie',
		'expire' => '+30 days'
	)
));

?>