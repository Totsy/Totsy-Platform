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
			'host' => '172.20.15.42',
			'persistent' => 'foo'),
		'test' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy_test',
			'host' => array('test'),
			'persistent' => 'foo'),
		'development' => array(
			'persistent' => true,	
			'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => 'db4.totsy.com'),
		'eric' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy_eric',
			'host' => 'localhost',
			'persistent' => 'foo'),
		'local' => array(
    		'persistent' => 'foo',
			//'replicaSet' => true,
    		'setSlaveOkay' => true,
    		'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => 'localhost',
			'adapter' => 'app\extensions\adapter\mongo\Db'
    		)
	));

?>
