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
		'production' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => '172.20.15.38'),
		'test' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy_test',
			'host' => '172.20.15.42'),
		'development' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy_dev',
			'host' => 'localhost'),
		'local' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => 'localhost')
	));

?>
