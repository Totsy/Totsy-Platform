<?php

use lithium\net\http\Router;

Router::connect('/files/dav', array(
	'library' => 'li3_dav', 'controller' => 'files', 'action' => 'dav'
));
Router::connect('/files/dav/{:token:[a-zA-Z0-9]+}', array(
	'library' => 'li3_dav', 'controller' => 'files', 'action' => 'dav'
));
Router::connect('/files/dav/{:token:[a-zA-Z0-9]+}/{:file:.*}', array(
	'library' => 'li3_dav', 'controller' => 'files', 'action' => 'dav'
));

?>