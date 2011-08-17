<?php

use lithium\net\http\Router;

Router::connect('/files/dav', array(
	'library' => 'li3_dav', 'controller' => 'files', 'action' => 'dav'
));
Router::connect('/files/dav/{:file:.*}', array(
	'library' => 'li3_dav', 'controller' => 'files', 'action' => 'dav'
));

?>