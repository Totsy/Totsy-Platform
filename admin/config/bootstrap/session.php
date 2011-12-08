<?php
use lithium\storage\Session;

Session::config(array(
'default' => array(
'adapter' => 'admin\extensions\adapter\session\Model',
'model' => 'MongoSession'
),
'flash_message' => array(
'adapter' => 'admin\extensions\adapter\session\Model',
'model' => 'MongoSession'
)
));

?>