<?php

use lithium\core\Libraries;
use lithium\core\Environment;

if (!Environment::is('production')) {
	Libraries::add('li3_docs');
	Libraries::add('li3_fixtures');
}


?>