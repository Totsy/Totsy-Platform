<?php
namespace app\extensions\command;

use app\models\Cart;
use lithium\core\Environment;
use app\extensions\command\Base;
use lithium\analysis\Logger;
use MongoDate;
use MongoId;
use lithium\storage\Session;
/**
* Removes expired items from users carts
**/
class CartCleaner extends Base {

    public $verbose = "false";

    /**
    * Set environment
    */
    public $env = 'development';

    /**
    * Set this to the number of minutes your expiration needs
    *
    **/
    public $minutes = 15;

    public function run() {
        Environment::set($this->env);
        $expire = $this->minutes * 60;
        $cartCollection = Cart::collection();
		//$deadline = new MongoDate(time() - $expire);
		$deadline = new MongoDate(time());
        $count =  $cartCollection->count(array("expires" => array('$lte' => $deadline)));
        $this->log("Removed $count cart items");
        $cartCollection->remove(array("expires" => array('$lte' => $deadline)));
		//Clean Promocodes/Credits/Services in sessions
		if (Session::read('promocode'))
			Session::delete('promocode');
		if (Session::read('credit'))
			Session::delete('credit');
		if (Session::read('services'))
			Session::delete('services');
		Session::delete('userSavings');
    }
}
?>