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
    $endOfFirstWeek = mktime(0, 0, 0, date("m"), 7, date("Y"));
    $endOfSecondWeek = mktime(0, 0, 0, date("m"), 14, date("Y"));
    $endOfThirdWeek =  mktime(0, 0, 0, date("m"), 21, date("Y"));
    $endOfMonth = mktime(0, 0, 0, date("m")+1, 1, date("Y"));
    
    
    //getting all the orders and Users from Mongo
    $orderCollections = Order::collection();
    $userCollections = User::collection();
    
    
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
    
    //sets the date conditions for the search for first 2 weeks of current month
     $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheMonth),
            '$lte' => new MongoDate($endOfSecondWeek)
        ));
    
   $First2WeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   //sets the date conditions for the search for today for the Users
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheMonth),
            '$lte' => new MongoDate($endOfSecondWeek)
        ));
        
   $First2WeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
     
    //sets the date conditions for the search for first 3 weeks of current month
     $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheMonth),
            '$lte' => new MongoDate($endOfThirdWeek)
        ));
    
   $First3WeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   //sets the date conditions for the search for today for the Users
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheMonth),
            '$lte' => new MongoDate($endOfThirdWeek)
        ));
   
   $First3WeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheMonth),
            '$lte' => new MongoDate($endOfMonth)
        ));
    
   $currentMonthRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   //sets the date conditions for the search for today for the Users
    $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheMonth),
            '$lte' => new MongoDate($endOfMonth)
        ));
   
   $currentMonthNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
  
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
    $endOfLastMonthFirstWeek = mktime(0, 0, 0, date("m")-1, 7, date("Y"));
    $endOfLastMonthSecondWeek = mktime(0, 0, 0, date("m")-1, 14, date("Y"));
    $endOfLastMonthThirdWeek = mktime(0, 0, 0, date("m")-1, 21, date("Y"));
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
            '$gt' => new MongoDate($startOfTheLastMonth),
            '$lte' => new MongoDate($endOfLastMonthSecondWeek)
            
        ));
        
   $lastMonthSecondWeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   
    
    //sets the date conditions for the search for first 2 weeks of current month
     $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheLastMonth),
            '$lte' => new MongoDate($endOfLastMonthSecondWeek)
        ));
    
   $lastMonthSecondWeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
    
    
    
   //*****************************************************************************************
   //sets the date conditions for the search for last month first 3 week for Users and Orders
   //*****************************************************************************************
   
    $conditions = array(
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheLastMonth),
            '$lte' => new MongoDate($endOfLastMonthThirdWeek)
            
        ));
        
   $lastMonthThirdWeekRevenue = $this->_revenue(array('conditions' => $conditions), $orderCollections);
   
   
    
    //sets the date conditions for the search for first 2 weeks of current month
     $conditions = array(
          'created_date' => array(
            '$gt' => new MongoDate($startOfTheLastMonth),
            '$lte' => new MongoDate($endOfLastMonthThirdWeek)
        ));
    
   $lastMonthThirdWeekNewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
    
    
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
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheLast3Month)
            
        ));
   $lastMonth3NewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
   
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
          'date_created' => array(
            '$gt' => new MongoDate($startOfTheLast6Month),
            '$lte' => new MongoDate($startOfTheLast3Month)
        ));
   $lastMonth6NewUsers = $this->_registration(array('conditions' => $conditions), $userCollections);
   
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
    'First2WeekRevenue',
    'First3WeekRevenue',
    'currentMonthRevenue',
    'todayNewUsers', 
    'yesterdayNewUsers', 
    'FirstWeekNewUsers', 
    'First2WeekNewUsers',
    'First3WeekNewUsers',
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
    'last6MonthNewUsers'
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

  protected function _registration($params, $data) {
      
    $conditions = $params['conditions'];
    $numberOfUsers = $data->find($conditions)->count();
   
    
    return $numberOfUsers;      
    
  }



}

?>