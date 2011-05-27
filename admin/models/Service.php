<?php

namespace admin\models;

class Service extends Base {
    /**
    Schema:
    {
        "_id" : ObjectId("4db1f6fd4f258f6c01000036"),
        "name" : "testing",
        "enabled" : true,
        "in_stock" : 100,
        "start_date" : ISODate("2011-04-20T04:00:00Z"),
        "end_date" : ISODate("2011-05-01T04:00:00Z"),
        "eligible_trigger" : {
            "trigger_type" : "cart_value",
            "trigger_action" : "pop_up",
            "trigger_value" : 50,
            "popup_text" : "Trigger went off!"
        },
        "upsell_trigger" : {
            "trigger_type" : "cart_value",
            "trigger_action" : "pop_up",
            "min_value" : "45.00",
            "max_value" : "49.99",
            "popup_text" : "You are so close to the goal.  don't stop!"
        },
        "logo_image" : "4daf4f974f258fe96f00000e"
    }

    **/
    public $_meta = array('source' => 'services');
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