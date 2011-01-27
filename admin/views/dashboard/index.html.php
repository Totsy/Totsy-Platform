<style type="text/css">
  
  th.dashboard {
  text-align: center;
  font-family: verdana, arial;
  background-color:#CEF6CE;
  }
  
  th.dashboard.sec_td{
  border-right:1px solid #000;
  background-color:#ddd;
  }
  
  td {
  font-size:12px;
  text-align: center;
  }
  
  th {
  font-size:12px;
  text-align: center;
  
  }
  
  .positive{
  
  font-size:12px;
  font-weight:bold;
  color:#00CC66;
  
  }
  
  .negative{
  
  font-size:12px;
  font-weight:bold;
  color:#FF0000;
  
  }
 </style>

<div class="container_16">

<?php
//date_default_timezone_set(ini_get('date.timezone'));
//echo "this is the ini call" . ini_get('date.timezone');

/**
echo "today is " . date('Y-m-d H:i:s', $today) . "<br><br>";
echo "yesterday at the same time is: " . date('y-m-d H:i', $yesterday_sametime) . "<br>";
echo "Start of Yesterday is: " . $startOfyesterday . "<br>";
echo "today's revenue is: $" . number_format( $todayRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . "<br>";
echo "today's revenue is: " . $yesterdayRevenue . "<br>";
echo "1st week revenue is: " . $FirstWeekRevenue . "<br>";
echo "first 2 weeks revenue is: " . $First2WeekRevenue . "<br>";  
echo "first 3 weeks revenue is: " . $First3WeekRevenue . "<br>";
echo "********************* New Users ******************** <br>";
echo "Today's Users is: " . $todayNewUsers . "<br>";
echo "Yesterday's Users is: " . $yesterdayNewUsers . "<br>";
echo "1st week Users is: " . $FirstWeekNewUsers . "<br>";
echo "first 2 weeks Users is: " . $First2WeekNewUsers . "<br>";  
echo "first 3 weeks Users is: " . $First3WeekNewUsers . "<br>";
echo "********************* Last Month Users ******************** <br>";


echo "last Month 1st week Users is: " . $lastMonthFirstWeekNewUsers . "<br>";
echo "last Month first 2 weeks Users is: " . $lastMonthSecondWeekNewUsers . "<br>";  
echo "last Month 3 weeks Users is: " . $lastMonthThirdWeekNewUsers . "<br>";
echo "last Month  Users is: " . $lastMonthNewUsers . "<br>";

echo "********************* Last Month Revenue ******************** <br>";

echo "last Month 1st week Users is: " . $lastMonthFirstWeekRevenue . "<br>";
echo "last Month first 2 weeks Users is: " . $lastMonthSecondWeekRevenue . "<br>";  
echo "last Month 3 weeks Users is: " . $lastMonthThirdWeekRevenue . "<br>";
echo "last Month  Users is: " . $lastMonthRevenue . "<br>";

 
 */

?>

<!-- 
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
    'todayNewUsers', 
    'yesterdayNewUsers', 
    'FirstWeekNewUsers', 
    'First2WeekNewUsers',
    'First3WeekNewUsers',
    'lastMonthFirstWeekRevenue',
    'lastMonthSecondWeekRevenue',
    'lastMonthThirdWeekRevenue',
    'lastMonthRevenue',
    'lastMonthFirstWeekNewUsers',
    'lastMonthSecondWeekNewUsers',
    'lastMonthThirdWeekNewUsers',
    'lastMonthNewUsers'
    

-->

<!-- Revenue result table -->
<div class="grid_8">
  <table class="datatable">
    <thead>
    <tr>
    <th colspan='3' class="dashboard"><h3>Revenue</h3></th>
    </tr>
   <tr>
    <th>
     <?php echo "Yesterday " . date('\a\t h:i A', $yesterday_sametime) ?>
    </th
    <th>
    <?php echo "Today " . date('m.d.y \a\t h:i A', $today) ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?php echo "$". number_format( $yesterdayRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?>
    </td>
    <td><?php echo "$". number_format( $todayRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?>
    </td>
    <td>
    <?php
    $percentage = ($yesterdayRevenue / $todayRevenue);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <thead>
    <tr>
    <th>
    1st week of <?php echo date('F Y', $startOfTheLastMonth) ?>
    </th>
    <th>
    1st week of <?php echo date('F Y', $today) ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= "$ " . number_format( $lastMonthFirstWeekRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= "$ " . number_format( $FirstWeekRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
    <?php
    $percentage = ($lastMonthFirstWeekRevenue / $FirstWeekRevenue);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <tr>
    <thead>
     <th>
    2nd week of <?php echo date('F Y', $startOfTheLastMonth) ?>
    </th>
    <th>
    2nd week of <?php echo date('F Y', $today) ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= "$ " . number_format( $lastMonthSecondWeekRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= "$ " . number_format( $First2WeekRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
    <?php
    $percentage = ($lastMonthSecondWeekRevenue / $First2WeekRevenue);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <thead>
    <tr>
     <th>
    3rd week of <?php echo date('F Y', $startOfTheLastMonth) ?>
    </th>
    <th>
    3rd week of <?php echo date('F Y', $today) ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= "$ " . number_format( $lastMonthThirdWeekRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= "$ " . number_format( $First3WeekRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
    <?php
    $percentage = ($lastMonthThirdWeekRevenue / $First3WeekRevenue);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <thead>
    <tr>
     <th>
    Total of <?php echo date('F Y', $startOfTheLastMonth) ?>
    </th>
    <th>
    <?php 
    if (date('d', $today) > 29){
    echo "Total of " . date('F Y', $today);
    }else{
    echo "As of " . date('jS F', $today);  
    }
     ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= "$ " . number_format( $lastMonthRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= "$ " . number_format( $currentMonthRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
    <?php
    $percentage = ($lastMonthRevenue / $currentMonthRevenue);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <thead>
    <tr>
    <th>
   <?php echo date('M jS',$startOfTheLast6Month) . " - " . date('M jS',$startOfTheLast3Month) ?>
   </th>
    <th><?php echo date('M jS',$startOfTheLast3Month) . " - " . date('M jS',$today) ?></th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= "$ " . number_format( $last6MonthRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= "$ " . number_format( $last3MonthRevenue , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
     <?php
    $percentage = ($last6MonthRevenue / $last3MonthRevenue);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
  </table>
  </div>


<!-- Registration result table -->
<div class="grid_8">


  <table class="datatable">
    <thead>
    <tr>
    <th colspan='3' class="dashboard"><h3 class="dashboard">Registrations</h3></th>
    </tr>
     <tr>
    <th>
     <?php echo "Yesterday " . date('\a\t h:i A', $yesterday_sametime) ?>
    </th
    <th>
    <?php echo "Today " . date('m.d.y \a\t h:i A', $today) ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?php echo $yesterdayNewUsers ?></td>
    <td><?php echo $todayNewUsers ?></td>
    <td>
    <?php
    $percentage = ($yesterdayNewUsers / $todayNewUsers);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <thead>
    <tr>
    <th>
    1st week of <?php echo date('F Y', $startOfTheLastMonth) ?>
    </th>
    <th>
    1st week of <?php echo date('F Y', $today) ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= number_format( $lastMonthFirstWeekNewUsers , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= number_format( $FirstWeekNewUsers , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
    <?php
    $percentage = ($lastMonthFirstWeekNewUsers / $FirstWeekNewUsers);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <tr>
    <thead>
     <th>
    2nd week of <?php echo date('F Y', $startOfTheLastMonth) ?>
    </th>
    <th>
    2nd week of <?php echo date('F Y', $today) ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= number_format( $lastMonthSecondWeekNewUsers , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= number_format( $First2WeekNewUsers , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
    <?php
    $percentage = ($lastMonthSecondWeekNewUsers / $First2WeekNewUsers);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <thead>
    <tr>
     <th>
    3rd week of <?php echo date('F Y', $startOfTheLastMonth) ?>
    </th>
    <th>
    3rd week of <?php echo date('F Y', $today) ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= number_format( $lastMonthThirdWeekNewUsers , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= number_format( $First3WeekNewUsers , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
    <?php
    $percentage = ($lastMonthThirdWeekNewUsers / $First3WeekNewUsers);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <thead>
    <tr>
     <th>
    Total of <?php echo date('F Y', $startOfTheLastMonth) ?>
    </th>
    <th>
    <?php 
    if (date('d', $today) > 29){
    echo "Total of " . date('F Y', $today);
    }else{
    echo "As of " . date('jS F', $today);  
    }
     ?>
    </th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td><?= number_format( $lastMonthNewUsers , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td><?= number_format( $currentMonthNewUsers , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) ?></td>
    <td>
    <?php
    $percentage = ($lastMonthNewUsers / $currentMonthNewUsers);
    if($percentage < 1){
      
      echo "<font class='positive'> + " . number_format( (1 - $percentage) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
      
    }else{
      
      echo "<font class='negative'> - " . number_format( ($percentage -1) * 100 , $decimals = 2 , $dec_point = '.' , $thousands_sep = ',' ) . " %</font>";
    }
     
    ?>
    </td>
    </tr>
    <thead>
    <tr>
    <th>Last 3 Month</th>
    <th>last 3 month before</th>
    <th>
    Diff %
    </th>
    </tr>
    </thead>
    <tr>
    <td>some number</td>
    <td>some number</td>
    <td></td>
    </tr>
  </table>

</div>


</div>

