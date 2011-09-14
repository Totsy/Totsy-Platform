<?php 

abstract class Sailthru {

	protected static $_client = null;

	public static function __init($config){
		static::registerAutoload();
		static::$_client = new Sailthru_Client(
			$config['mail']['api_key'], 
			$config['mail']['secret'], 
			$config['mail']['api_url']
		);  
	}

	public static function autoload ($class_name){

		$exists = false; 

		$path = dirname(__FILE__).'/sailthru-php5-client/sailthru/'.$class_name . '.php';
		if(file_exists($path)) { $exists= true;	}

		if($exists === true) { 
			require_once $path;
		}
	}

	public static function registerAutoload() {
    	spl_autoload_register(array('Sailthru', 'autoload'));
  	}
  	
  	public static function __callStatic($name,$args){
  		call_user_func_array(array(static::$_client,$name),$args);
  	}
}

?>