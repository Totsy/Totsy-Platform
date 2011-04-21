<?php

namespace admin\models;

class Service extends Base {

    public $validates = array(
	    "name" => array(
	        array('notEmpty', 'message' => 'Please enter a name')
	    ),
	    "trigger_value" => array(
	        array("notEmpty", "message" => "Please enter a value for the service to activate."),
	        array("numeric", "message" => "Value must be numeric. eg. 1234")
	    ),
	    'start_date' => array(
	        array("notEmpty", "message" => "Please enter a start date"),
	        array("date", "message" => "Please enter a valid date")
	    ),
	    'end_date' => array(
	        array("notEmpty", "message" => "Please enter an end date")
	    )
	);
}

?>