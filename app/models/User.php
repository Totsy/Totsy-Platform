<?php

namespace app\models;

use \lithium\data\Connections;

class User extends \lithium\data\Model {
	
	public static function update(array $criteria = array(), array $data = array()) {
		$self = static::_instance();
		$classes = $self->_classes;
		$meta = array('model' => get_called_class()) + $self->_meta;
		$params = compact('criteria', 'data');
		
		$db = Connections::get('default');
		//var_dump($db->connection->totsy->users->update(array('Test'=>'New'), array('$set' => array('Test' => 'Old'))));
		
		var_dump($self::invokeMethod('_connection'));

		
		
//		$db = Connections::get('default', array('autoCreate' => false));
//		//Find a better way to connect to MongoCollection::command() directly.
//		$db->connection->totsy->users->update($criteria, $options);

	}
}


?>