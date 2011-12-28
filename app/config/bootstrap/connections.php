<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\data\Connections;


// MongoDB Connection

Connections::add('default', array(
    'production' => array(
            'type' => 'MongoDb',
            'database' => 'totsy',
            'setSlaveOkay' => false,
            'replicaSet' =>'totsy',
            'host' => array(
                'db2-dc1.totsy.com',
                'db3-dc1.totsy.com'
            ),
        'adapter' => 'app\extensions\adapter\data\source\MongoDb'),
    'test' => array(
            'type' => 'MongoDb',
            'database' => 'totsy',
            'host' => 'localhost',
            'adapter' => 'app\extensions\adapter\data\source\MongoDb'),
    'staging' => array(
            'type' => 'MongoDb',
            'database' => 'totsy',
            'host' => 'db1-dc1.totsystaging.com',
            'adapter' => 'app\extensions\adapter\data\source\MongoDb'),
    'development' => array(
        'type' =>  'MongoDb',
        'database' => 'totsy',
        'host' => 'localhost',
    ),
    'local' =>array(
        'type' =>  'MongoDb',
        'database' => 'totsy',
        'host' => array(
            'localhost'
        ),
        'adapter' => 'app\extensions\adapter\data\source\MongoDb'
    )
));

?>
