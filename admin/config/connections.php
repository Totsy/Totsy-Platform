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
			'setSlaveOkay' => true,
			'replicaSet' => true,
			'host' => array(
    			'db1',
    			'db2',
    			'db3',
    			'db4',
    			'db5',
    			'db6' ),
			'adapter' => 'admin\extensions\adapter\mongo\Db'),
		'test' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy_test',
			'host' => array('test')),
		'development' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => 'localhost'),
		'local' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => 'localhost')
	));

?>
