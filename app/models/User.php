<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;



/**
 * @todo Optimize method addressUpdate so it can be used to add and remove an address
 *	Make sure that when adding or removing an address that we $inc (+/-) the counter.
 *  Currently we only allowing 10 addresses to be stored in MongoDB. This will be hard coded
 *  until we implement a configuration setting.
 */

class User extends \lithium\data\Model {
}


?>