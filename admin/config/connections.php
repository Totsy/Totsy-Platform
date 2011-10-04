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
                'type' => 'MongoDb',
                'database' => 'totsy',
                'setSlaveOkay' => false,
                'replicaSet' =>'totsy',
                'host' => array(
                'db1',
                'db2',
                'db3'
                )),
		'test' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => array('devdb2.totsy.com')),
		'development' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => 'localhost'),
		'local' => array(
			'type' =>  'MongoDb',
			'database' => 'totsy',
			'host' => array(
    			'localhost' ))
		));
?>
