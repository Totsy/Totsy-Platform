<?php

namespace admin\extensions\sabre\dav;

use lithium\util\String;
use admin\models\File;
use MongoRegex;
use Exception;
use Sabre_DAV_Exception_Forbidden;
use Sabre_DAV_Exception_FileNotFound;

class FlatDirectory implements \Sabre_DAV_ICollection {}

?>