<?php

namespace app\extensions\command;

use app\models\Affiliate;
use app\models\Order;
use MongoRegex;
use lithium\core\Environment;
use app\models\User;

/**
    This is li3 command class is used to send linkshare (affiliate) canceled orders of any linkshare
    referred user.
**/

class OrderCancel extends \lithium\console\Command {

    public $env = 'development';
    public function run() {
        Environment::set($this->env);

        //Obtain a list of all linkshare users that have order at least once
        $linkshareUsers = User::find('all', array('conditions' => array(
                                'invited_by' => new MongoRegex('/linkshare/i'),
                                'purchase_count' => array('$gte' => 1)
                            )));
        //retrieve all canceled orders per user and send the information to linkshare
        foreach($linkshareUsers as $user){
            //Canceled orders
            $ord_count = Order::count(array(
                            'user_id' => (string) $user->_id,
                            'cancel' => true
                        ));
            $this->out("There are $ord_count canceled orders");
            if($ord_count > 0){
                $orders = Order::find('all', array('conditions' => array(
                            'user_id' => (string) $user->_id,
                            'cancel' => true
                        )));

                foreach($orders as $order){
                    Affiliate::generatePixel('linkshare','',array(
                        'orderid' => $order->order_id,
                        'trans_type' => 'cancel'
                    ));
                }
            }
            //Orders with canceled items lines
             $item_count = Order::count(array(
                                'user_id'=> (string) $user->_id,
                                'items.cancel' => true
                            ));
            $this->out("There are $item_count canceled orders");
            if($item_count > 0) {
                $cancel_items = Order::find('all', array('conditions' => array(
                                                'user_id' => (string) $user->_id,
                                                'items.cancel' => true
                                            )));
                foreach($cancel_items as $order){
                    Affiliate::generatePixel('linkshare','',array('orderid' => $order->order_id, 'trans_type' => 'cancel'));
                }
            }

        }

    }
}
?>
