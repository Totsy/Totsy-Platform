<?php

namespace admin\controllers;

use admin\models\Order;
use admin\models\User;
use MongoDate;
use MongoCode;

class DashboardController extends \lithium\action\Controller {

    public function index() {
    
    //set the timezone to the US Eastern time
    //date_default_timezone_set(ini_get('date.timezone')); 
    
    //sets today date with time of the day.
    $date_test = mktime(0, 0, 0, date("09"), date("01"), date("2010"));
    $startOfToday = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    $today = strtotime('now');
    
    
    //sets yesterday with timeof day based on today's time. basically yesterday at exact same time as today
    $yesterday_sametime = strtotime('-1 day');
    $startOfyesterday = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
    
    //set the date variables for 
    // start of the month current month
    // end of first week current month
    // end of 2nd week current month
    // end of 3 week current month
    // end of month current month
    $startOfTheMonth  = mktime(0, 0, 0, date("m"), 1, date("Y"));
    $endOfFirstWeek = mktime(0, 0, 0, date("m"), 8, date("Y"));
    $endOfSecondWeek = mktime(0, 0, 0, date("m"), 15, date("Y"));
    $endOfThirdWeek =  mktime(0, 0, 0, date("m"), 22, date("Y"));
    $endOfMonth = mktime(0, 0, 0, date("m")+1, 1, date("Y"));
    
    
    //getting all the orders and Users from Mongo
    $orderCollections = Order::collection();
    $userCollections = User::collection();
    
    
    $conditions = array(
          'created_date' => array(
            '$lte' => new MongoDate($date_test)
        ));
    
    $total_NewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
    
    //sets the date conditions for the search for today for the Orders
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfToday),
            '$lte' => new MongoDate($today)
        ));
    
    
    $todayRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
    
    //sets the date conditions for the search for today for the Users
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfToday),
            '$lte' => new MongoDate($today)
        ));
    
    
    $todayNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
    
    //sets the date conditions for the search for yesterday
     $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfyesterday),
            '$lte' => new MongoDate($yesterday_sametime)
        ));
    
   $yesterdayRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   //sets the date conditions for the search for today for the Users
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfyesterday),
            '$lte' => new MongoDate($yesterday_sametime)
        ));
   
   $yesterdayNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
     
    
    //sets the date conditions for the search for 1st week of current month
     $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheMonth),
            '$lte' => new MongoDate($endOfFirstWeek)
        ));
    
   $FirstWeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   //sets the date conditions for the search for today for the Users
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheMonth),
            '$lte' => new MongoDate($endOfFirstWeek)
        ));
   
   
   $FirstWeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
    
    //sets the date conditions for the search for the second  weeks of current month
     $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($endOfFirstWeek),
            '$lte' => new MongoDate($endOfSecondWeek)
        ));
   
  
   if ($today > $endOfFirstWeek) {
   
   $secondWeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
    }else{
      
      $secondWeekRevenue = 0;
    }
    
   //sets the date conditions for the search for today for the Users
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($endOfFirstWeek),
            '$lte' => new MongoDate($endOfSecondWeek)
        ));
        
   if ($today > $endOfFirstWeek) {
   
   $secondWeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
    }else{
      
      $secondWeekNewUsers = 0;
    }
   
     
    //sets the date conditions for the search for first 3 weeks of current month
     $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($endOfSecondWeek),
            '$lte' => new MongoDate($endOfThirdWeek)
        ));
    
    
   if ($today > $endOfSecondWeek) {
   
   $thirdWeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
    }else{
      
      $thirdWeekRevenue = 0;
    }
     
   
   
   //sets the date conditions for the search for today for the Users
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($endOfSecondWeek),
            '$lte' => new MongoDate($endOfThirdWeek)
        ));
   
   
   if ($today > $endOfSecondWeek) {
   
   $thirdWeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
    }else{
      
      $thirdWeekNewUsers = 0;
    }
    
   
   
   
   $conditions = array(
         'date_created' => array(
            '$gt' => new MongoDate($endOfThirdWeek),
            '$lte' => new MongoDate($endOfMonth)
        ));
   
   if ($today > $endOfThirdWeek) {
   
   $currentMonthRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections)
   + ($FirstWeekRevenue + $secondWeekRevenue + $thirdWeekRevenue);
   
    }else{
      
      $currentMonthRevenue = $FirstWeekRevenue + $secondWeekRevenue + $thirdWeekRevenue;
   
    }
     
   
   //sets the date conditions for the search for today for the Users
   $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($endOfThirdWeek),
            '$lte' => new MongoDate($endOfMonth)
        ));
   
    if ($today > $endOfThirdWeek) {
   
   $currentMonthNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections) + 
   ($FirstWeekNewUsers + $secondWeekNewUsers + $thirdWeekNewUsers);
    
    }else{
      
      $currentMonthNewUsers = $FirstWeekNewUsers + $secondWeekNewUsers + $thirdWeekNewUsers;
   
    }
    
   
   
  
  //*****************************************************************************************************
  //********************************   end of current months number   ***********************************
  //*****************************************************************************************************
  
  //*****************************************************************************************************
  //********************************   Beginning of Last months number   ********************************
  //*****************************************************************************************************
  
    
    //set the date variables for 
    // start of date the last month
    // end of first week last month
    // end of 2nd week last month
    // end of 3 week last month
    // end of month last month
    
    $startOfTheLastMonth  = mktime(0, 0, 0, date("m")-1, 1, date("Y"));
    $endOfLastMonthFirstWeek = mktime(0, 0, 0, date("m")-1, 8, date("Y"));
    $endOfLastMonthSecondWeek = mktime(0, 0, 0, date("m")-1, 15, date("Y"));
    $endOfLastMonthThirdWeek = mktime(0, 0, 0, date("m")-1, 22, date("Y"));
    $endOfLastMonth = mktime(0, 0, 0, date("m"), 1, date("Y"));
    $startOfTheLast3Month  = mktime(0, 0, 0, date("m")-3, date("d"), date("Y"));
    $startOfTheLast6Month  = mktime(0, 0, 0, date("m")-6, date("d"), date("Y"));
   
  //*****************************************************************************************
   //sets the date conditions for the search for last month first  week for Users and Orders
   //*****************************************************************************************
  
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheLastMonth),
            '$lte' => new MongoDate($endOfLastMonthFirstWeek)
            
        ));
        
   $lastMonthFirstWeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
     $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheLastMonth),
            '$lte' => new MongoDate($endOfLastMonthFirstWeek)
        ));
    
   $lastMonthFirstWeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
   //*****************************************************************************************
   //sets the date conditions for the search for last month first 2 week for Users and Orders
   //*****************************************************************************************
   
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($endOfLastMonthFirstWeek),
            '$lte' => new MongoDate($endOfLastMonthSecondWeek)
            
        ));
        
   
   if ($today > $endOfFirstWeek) {
   
   $lastMonthSecondWeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
    }else{
      
      $lastMonthSecondWeekRevenue = 0;
    }
   
   
   
   
   
    
    //sets the date conditions for the search for first 2 weeks of current month
     $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($endOfLastMonthFirstWeek),
            '$lte' => new MongoDate($endOfLastMonthSecondWeek)
        ));
    
   
   if ($today > $endOfFirstWeek) {
   
   $lastMonthSecondWeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
    }else{
      
       $lastMonthSecondWeekNewUsers = 0;
    
    }
   
   
   
    
    
    
   //*****************************************************************************************
   //sets the date conditions for the search for last month first 3 week for Users and Orders
   //*****************************************************************************************
   
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($endOfLastMonthSecondWeek),
            '$lte' => new MongoDate($endOfLastMonthThirdWeek)
            
        ));
   
   
   if ($today > $endOfSecondWeek) {
   
   $lastMonthThirdWeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
    }else{
      
       $lastMonthThirdWeekRevenue = 0;
    
    }     
   
   
   
    
    //sets the date conditions for the search for first 2 weeks of current month
     $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($endOfLastMonthSecondWeek),
            '$lte' => new MongoDate($endOfLastMonthThirdWeek)
        ));
   
   if ($today > $endOfSecondWeek) {
   
   $lastMonthThirdWeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
    }else{
      
     //$lastMonthThirdWeekNewUsers = 0;
    
    }  
     
   
    
    
    //*****************************************************************************************
   //sets the date conditions for the search for last month for Users and Orders
   //*****************************************************************************************
   
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheLastMonth),
            '$lte' => new MongoDate($endOfLastMonth)
            
        ));
    
   
   $lastMonthRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
  
   
   
   
    
    //sets the date conditions for the search for first 2 weeks of current month
     $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheLastMonth),
            '$lte' => new MongoDate($endOfLastMonth)
        ));
   
   
   
   $lastMonthNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
  
   
   
   
    //*****************************************************************************************
   //sets the date conditions for the search for last 3 month for Users and Orders
   //*****************************************************************************************
   
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheLast3Month)
            
        ));
        
   $last3MonthRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   
    
    //sets the date conditions for the search for first 2 weeks of current month
     $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheLast3Month)
            
        ));
   $last3MonthNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   $last3MonthNewUsers += 81300;
   
    //*****************************************************************************************
   //sets the date conditions for the search for 3 to 6 month for Users and Orders
   //*****************************************************************************************
   
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheLast6Month),
            '$lte' => new MongoDate($startOfTheLast3Month)
            
        ));
        
   $last6MonthRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   
    
    //sets the date conditions for the search for first 2 weeks of current month
     $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheLast6Month),
            '$lte' => new MongoDate($startOfTheLast3Month)
        ));
        
   $last6MonthNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   $last6MonthNewUsers += 81300;
   
   $conditions = array();
        
   $totalUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
   /**
   ////////////////////////////////////////////////////////////////////////
  
   $start= mktime(0, 0, 0, date("m")-1, 1, date("Y"));
   $end = mktime(0, 0, 0, date("m"), 1, date("Y"));
   
   //$conditions = array(
   //       'created_date' => array(
   //         '$gt' => new MongoDate($start_for_selected),
   //         '$lte' => new MongoDate($end_for_selected)
   //     ));
    
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($start),
            '$lte' => new MongoDate($end)
        ));    
    
   //$totalSelectedUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
   $totalSelectedUsers = $this->_selectedUsers(array('conditions' => $conditions), $userCollections);
   //foreach($totalSelectedUsers as $user) {
     //var_dump($user);
   //}
   //var_dump($totalSelectedUsers);
   
   $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($start),
            '$lte' => new MongoDate($end)
            
        ));
   echo "test 1";
   $totalRevenueSelectedUsers = $this->_ltv(array('conditions' => $conditions), $orderCollections, $totalSelectedUsers);
   
   ////////////////////////////////////////////////////////////////////////
   **/
   
    //Return all the goodies to the Dashboard View
    
    return compact(
    'today', 
    'yesterday_sametime', 
    'startOfToday', 
    'startOfyesterday', 
    'todayRevenue', 
    'todayRegistration', 
    'todayRevenue',
    'yesterdayRevenue', 
    'FirstWeekRevenue',
    'secondWeekRevenue',
    'thirdWeekRevenue',
    'currentMonthRevenue',
    'todayNewUsers', 
    'yesterdayNewUsers', 
    'FirstWeekNewUsers', 
    'secondWeekNewUsers',
    'thirdWeekNewUsers',
    'currentMonthNewUsers',
    'lastMonthFirstWeekRevenue',
    'lastMonthSecondWeekRevenue',
    'lastMonthThirdWeekRevenue',
    'lastMonthRevenue',
    'lastMonthFirstWeekNewUsers',
    'lastMonthSecondWeekNewUsers',
    'lastMonthThirdWeekNewUsers',
    'lastMonthNewUsers',
    'startOfTheLastMonth',
    'startOfTheLast3Month',
    'startOfTheLast6Month',
    'last3MonthRevenue',
    'last3MonthNewUsers',
    'last6MonthRevenue',
    'last6MonthNewUsers',
    'totalUsers',
    'total_NewUsers',
    'totalSelectedUsers',
    'totalRevenueSelectedUsers'
    );
  }


  protected function _revenue($params, $data) {
  
    $conditions = $params['conditions'];
    $orders = $data->find($conditions);
  
  if ($orders) {
   $totalRevenue = 0;
        foreach ($orders as $order) {
        $totalRevenue += $order['total'];
     }
         
    return $totalRevenue;             
    }

  }

  protected function _ltv($params, $data, $selected_users) {
    
  
  $data->ensureIndex(array('date_created' => 1));
  //echo "test 2";
    $conditions = $params['conditions'];
    $orders = $data->find($conditions,array(
                        'user_id' => 1, 'total' => 1));
      echo "test 3"; 
     
    //var_dump($orders);
  if ($orders) {
    echo "test 4";
   $totalRevenue = 0;
        foreach ($orders as $order) {
           //var_dump($order['user_id']);
          
          foreach($selected_users as $user){
            //var_dump($order["user_id"]);
            
            
            if($user["_id"]  == new MongoId($order["user_id"])) {
            $totalRevenue += $order["total"];
            }
              
             
          }
        
        }
        
  }
  
         
    echo $totalRevenue;             
  
 }

  

  protected function _selectedUsers($params, $data) {
    
    $data->ensureIndex(array('created_date' => 1));  
    $conditions = $params['conditions'];
    //var_dump($conditions);
    $s_users = $data->find($conditions, array(
                        '_id' => 1));
   
    
    return $s_users;      
    
  }
  protected function _registration($params, $data) {
      
    $conditions = $params['conditions'];
    $numberOfUsers = $data->find($conditions)->count();
   
    
    return $numberOfUsers;      
    
  }



}

?>