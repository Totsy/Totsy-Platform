<?php

namespace admin\models;

/**
 *
*{{{
*
* { queue:
*	{orders:[event_ids]}
*	{purchase_orders: [event_ids]}
*
* {summary:{
*   order_file: {{lines:int}, {orders:int}, {filename:text}}
*   item_file:{count:int}, {filename:text}
*   purchase_order_file: {filenames: [event_id:name]}
* }}
*}
*}}}
 */
class Queue extends \lithium\data\Model {

	public $validates = array();

	protected $_meta = array('source' => 'queue');

}

?>