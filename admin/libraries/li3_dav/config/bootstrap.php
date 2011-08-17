<?php

use lithium\core\Libraries;

Libraries::add('Sabre', array(
	'prefix' => 'Sabre_',
	'path' => dirname(__DIR__) . '/libraries/Sabre',
	'bootstrap' => 'autoload.php'
));

?>