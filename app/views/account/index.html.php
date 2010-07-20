<?php $this->title("Account Dashboard for " . $userInfo['firstname']); ?>
<?=$this->html->script('jquery.equalheights'); ?>
<?=$this->menu->render('left'); ?>
<h1 class="p-header">My Account</h1>

<div class="tl"></div>
<div class="tr"></div>
<div id="page">

	<!-- Replace with user's name -->
	<strong>Hello <?=$userInfo['firstname']?></strong>
	
	<!-- Replace with account welcome message -->
	<p>From your My Account Dashboard you have the ability to view a snapshot of your recent account activity and update your account information. Select a link below to view or edit information.</p>
	
	<h2 class="gray"><?php echo ('Account Information');?></h2>
	
	<div class="col-2">
	
		<div class="r-container box-2 fl">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="r-box lt-gradient-1">
				<h3 class="gray fl"><?php echo ('Contact Information');?></h3>&nbsp;|&nbsp;<?=$this->html->link('Edit Info', '/account/info');?>
				<br />
				<br />
				<?=$userInfo['firstname'].' '.$userInfo['lastname'] ?><br />
				<?=$userInfo['email']?><br />
				<a href="#" title="<?php echo ('Change Password');?>"><?php echo ('Change Password');?></a>
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>

		<div class="r-container box-2 fr">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="r-box lt-gradient-1">
				<h3 class="gray fl">Newsletter - Coming Soon</h3>&nbsp;|&nbsp;<a href="" title="Edit">Edit</a>
				<br />
				<br />
				<dl>
					<dt>You are currently subscribed to:</dt>
					<dd>
						<ul>
							<li>General Subscription</li>
						</ul>
					</dd>
				</dl>
				
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
	
	</div>
	
	<h2 class="gray fl"><?php echo ('Address Book');?></h2>&nbsp;|&nbsp;<?=$this->html->link('Manage Addresses', '/addresses/view');?>
	
	<div class="col-2">
	
		<div class="r-container box-2 fl">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="r-box lt-gradient-1">
				<?php
					if(!isset($addresses['Billing'])){						
						$billMessage = 'No Billing Address';
						$billLinkText = $routing['message'];
						$billLink = $routing['url'];
					} else {
						$billLinkText = 'Edit Address';
						$billLink = $addresses['Billing']['url'];
					}
					if(!isset($addresses['Shipping'])){						
						$shipMessage = 'No Shipping Address';
						$shipLinkText = $routing['message'];
						$shipLink = $routing['url'];
					} else {
						$shipLinkText = 'Edit Address';
						$shipLink = $addresses['Shipping']['url'];
					}			
				?>
				<h3 class="gray fl"><?php echo ('Primary Billing Address');?></h3>&nbsp;|&nbsp;
				<?=$this->html->link($billLinkText, $billLink);?>
				<br />
				<br />
				<address>
					<?php
						if(isset($addresses['Billing'])) {
							echo $addresses['Billing']['address'] . "<br />";
							echo $addresses['Billing']['address_2'] . "<br />";
							$subArray = array(
									$addresses['Billing']['city'], 
									$addresses['Billing']['state'], 
									$addresses['Billing']['zip'],
									$addresses['Billing']['country']
									);
							echo implode(', ', $subArray);
							
						} else {
							echo $billMessage;
						}
					?>				
				</address>
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
		
		<div class="r-container box-2 fr">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="r-box lt-gradient-1">
				
				<h3 class="gray fl"><?php echo ('Primary Shipping Address');?></h3>&nbsp;|&nbsp;<?=$this->html->link($shipLinkText, $shipLink);?>
				<br />
				<br />
				<address>
					<?php
						if(isset($addresses['Shipping'])) {
							echo $addresses['Shipping']['address'] . "<br />";
							echo $addresses['Shipping']['address_2'] . "<br />";
							$subArray = array(
									$addresses['Shipping']['city'], 
									$addresses['Shipping']['state'], 
									$addresses['Shipping']['zip'],
									$addresses['Shipping']['country']
									);
							echo implode(', ', $subArray);
							
						} else {
							echo $shipMessage;
						}
					?>
				</address>
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
	
	</div>

</div>
<div class="bl"></div>
<div class="br"></div>
<!-- This equals the hight of all the boxes to the same height -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".r-box").equalHeights(100,300);
	});
</script>
