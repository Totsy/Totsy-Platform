<?php
	use app\models\Menu;

	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	$leftMenu = $this->MenuList->build($menu, $options);

	echo $leftMenu;

?>

<div class="tl"></div> 
<div class="tr"></div> 
<div id="page"> 
<?=$this->form->create(null,array('id'=>'addressForm', 'class' => "fl"));?> 
	<h2 class="gray mar-b">Email Preferences</h2> 
	
	<form action="#" class="fl"> 
		<div class="email-prefs"> 
			<div class="first"> 
				<fieldset> 
					<legend>Daily</legend> 
					
					<ul> 
						<li><label><input type="checkbox" name="womens-reminder-daily" value="Womens Sale Reminder" /> Womens Sale Reminder</label></li> 
						<li><label><input type="checkbox" name="toys-reminder-daily" value="Toys Sale Reminder" /> Toys Sale Reminder</label></li> 
						<li><label><input type="checkbox" name="boys-reminder-daily" value="Boys Sale Reminder" /> Boys Sale Reminder</label></li> 
						<li><label><input type="checkbox" name="girls-reminder-daily" value="Girls Sale Reminder" /> Girls Sale Reminder</label></li> 
					</ul> 
						
				</fieldset> 
			</div> 
			
			<div class="middle"> 
				<fieldset> 
					<legend>Weekly</legend> 
					
					<ul> 
						<li><label><input type="checkbox" name="womens-reminder-weekly" value="Womens Sale Reminder" /> Womens Sale Reminder</label></li> 
						<li><label><input type="checkbox" name="toys-reminder-weekly" value="Toys Sale Reminder" /> Toys Sale Reminder</label></li> 
						<li><label><input type="checkbox" name="boys-reminder-weekly" value="Boys Sale Reminder" /> Boys Sale Reminder</label></li> 
						<li><label><input type="checkbox" name="girls-reminder-weekly" value="Girls Sale Reminder" /> Girls Sale Reminder</label></li> 
					</ul> 
				
				</fieldset> 
			</div> 
			
			<div class="last"> 
				<fieldset> 
					<legend>Special</legend> 
					
					<ul> 
						<li><label><input type="checkbox" name="sent-invitations" value="Sent Invitations Updates" /> Sent Invitations Updates</label></li> 
						<li><label><input type="checkbox" name="special-offers" value="Special Offers" /> Special Offers</label></li> 
					</ul> 
					
				</fieldset> 
			</div> 
		</div> 
		
		<p class="clear mar-t"> 
			<button type="submit" name="submit" class="flex-btn fr"><span>Submit</span></button> 
			<strong>Based on your preferences:</strong><br /> 
			You will receive an average of <strong>3</strong> emails from Totsy per week
		</p> 

<?=$this->form->end();?> 