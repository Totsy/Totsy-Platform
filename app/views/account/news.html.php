<?php $this->title("Email Preferences for " . $userInfo['firstname']); ?>
<h1 class="p-header">Email Preferences</h1>

<div id="left">
	<ul class="menu main-nav">
		<li class="firstitem17 active"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
		<li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
		<li class="item18"><a href="/account/password" title="Change Password"><span>Change Password</span></a></li>
		<li class="item19"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
		<li class="item20"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
		<li class="item20"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
		<li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
		<br />
		<h3 style="color:#999;">Need Help?</h3>
		<hr />
		<li class="first item18"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
		<li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
	</ul>
</div>

<div class="tl"></div> 
<div class="tr"></div> 
<div id="page"> 
<?php echo $this->form->create(null,array('id'=>'addressForm', 'class' => "fl"));?> 
	<h2 class="gray mar-b">Email Preferences</h2> 
	<hr />
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

<?php echo $this->form->end();?> 
