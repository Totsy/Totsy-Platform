<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \lithium\data\Connections;


// MongoDB Connection
    Connections::add('default', array(
		'type' =>  'MongoDb', 
		'database' => 'totsy', 
		'host' => 'localhost'
	));
    
?>