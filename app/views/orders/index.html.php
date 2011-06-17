<?php $this->title("My Orders"); ?>

<div class="grid_16">
	<h2 class="page-title gray">My Orders</h2>
	<hr />
</div>

<div class="grid_4">
	<div class="roundy grey_inside">
		<h3 class="gray">My Account</h3>
		<hr />
		<ul class="menu main-nav">
		<li><a href="/account" title="Account Dashboard">Account Dashboard</a></li>
		<li><a href="/account/info" title="Account Information">Account Information</a></li>
		<li><a href="/account/password" title="Change Password">Change Password</a></li>
		<li class="active"><a href="/addresses" title="Address Book">Address Book</a></li>
		<li><a href="/orders" title="My Orders">My Orders</a></li>
		<li><a href="/Credits/view" title="My Credits">My Credits</a></li>
		<li><a href="/Users/invite" title="My Invitations">My Invitations</a></li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Need Help?</h3>
		<hr />
		<ul class="menu main-nav">
		    <li><a href="/tickets/add" title="Contact Us">Help Desk</a></li>
			<li><a href="/pages/faq" title="Frequently Asked Questions">FAQ's</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/terms" title="Terms Of Use">Terms Of Use</a></li>
		</ul>
	</div>
</div>

<div class="grid_11 omega roundy grey_inside b_side">

	<h2 class="page-title gray">My Orders
	<span style="margin-left:290px;">Your Lifetime Savings : </span><span class="fr">
		<?php if (!empty($lifeTimeSavings)) : ?>
			<span style="color:#009900; font-size:18px; float:right;">$<?=number_format((float) $lifeTimeSavings, 2);?></span>
		<?php endif ?></span></h2>
		<div class="clear"></div>
	<hr />
	
		<!--		<h2 class="gray mar-b">Tracking System <span style="float:right; font-weight:normal; font-size:11px;"><span style="font-weight:bold;">NOTE: </span>Orders may be split into multiple shipments with different tracking numbers.</span></h2>
		<hr />
		<ol class="shipping-process">
			<li class="placed link" id="placed-btn">Order Placed With Totsy</li>
			<li class="secured link" id="secured-btn">Order Secured From Partners</li>
			<li class="warehouse link" id="warehouse-btn">Items Arrive At Warehouse</li>
			<li class="shipped link" id="shipped-btn">Packaged Shipped To You</li>
			<li class="recieved link" id="recieved-btn">Order Arrives At Your Home</li>
		</ol>
        	<p style="border:1px solid #ddd; background:#f7f7f7; padding:10px; font-size:14px; text-align:center; color:red;">Our order tracking system is currently under construction. <br />
		All orders  are being processed and will be shipped within 15 to 20 business days. <br /> 
		If you have any questions do not hesitate to contact us!</p>
-->		<?php if ($orders->data()): ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="order-table">
		
			<thead>
				<tr>
					<th width="18%">Order Date</th>
					<th width="10%">Order ID</th>
					<th width="40%">Items </th>
					<th width="50%">Shipping Info</th>
					<!-- <th width="30%">Tracking</th> -->
				</tr>
			</thead>
			
			<tbody>
				<?php foreach ($orders as $order): ?>
					<?php if(empty($order->cancel)): ?>
					<tr class="alt$x" style="border-bottom:1px solid #ddd;">
						<td><?=date('M d, Y', $order->date_created->sec); ?></td>
						<td>
							<?php if (!empty($order->order_id)): ?>
								<?=$this->html->link("$order->order_id", array(
									'Orders::view',
									'args' => $order->order_id
									));
								?>
							<?php endif ?>
						</td>
						<?php if ($order->items): ?>
							<?php $items = $order->items->data() ?>
						<?php endif ?>
						<td>
						<?php foreach ($items as $item): ?>
							<?php if(empty($item["cancel"])) : ?>
								<strong><?=$item['description']?></strong><br />
								<span style="font-size:12px;">Color: <?=$item['color']?></span><br />
								<span style="font-size:12px;">Size: <?=$item['size']?></span><br />
								<span style="font-size:12px;">Quantity: <?=$item['quantity']?></span><br />
							<?php endif ?>
						<?php endforeach ?>
						</td>
						<td>
							<?php if (!empty($trackingNumbers) || !empty($order->tracking_numbers)): ?>
								Tracking Number(s):
								<?php if ($trackingNumbers): ?>
									<?php if (!empty($trackingNumbers["$order->_id"])): ?>
										<?php foreach ($trackingNumbers["$order->_id"] as $trackingNumber): ?>
											<?=$this->shipment->link($trackingNumber['code'], array('type' => $trackingNumber['method']))?>
										<?php endforeach ?>
									<?php endif ?>
								<?php endif ?>
								<?php if (!empty($order->tracking_numbers)): ?>
									<?php foreach ($order->tracking_numbers as $number): ?>
										<?=$this->shipment->link($number, array('type' => 'UPS'))?>
									<?php endforeach ?>
								<?php endif ?>
							<?php else: ?>
								<?php if ($shipDate["$order->_id"] > time()): ?>
									Estimated Ship Date: <br/><?=date('M d, Y', $shipDate["$order->_id"]); ?>
								<?php else: ?>
									-
								<?php endif ?>
						<?php endif ?>
						</td>
					</tr>
					<?php endif ?>
				<?php endforeach ?>
			</tbody>
		
		</table>
		<?php else: ?>
			<center><strong>You do not have any orders. <a href="/" title="Go Shopping">Go Shopping</a></strong></center>
		<?php endif ?>
	<br />

</div>
</div>
<div class="clear"></div>




<script type="text/javascript" src="../js/jquery.equalheights.js"></script>
<script type="text/javascript" src="../js/jquery.tools.min.js"></script>

<!-- This equals the hight of all the boxes to the same height -->
<script type="text/javascript">
	$(document).ready(function() {
		
		$(".r-box").equalHeights(100,300);
		
		//process modal pop-ups
		//$("#placed-modal").dialog({height:250,modal:true,draggable:false,resizable:false,width:680,autoOpen:false});
		//$("#secured-modal").dialog({height:250,modal:true,draggable:false,resizable:false,width:680,autoOpen:false});
		//$("#warehouse-modal").dialog({height:250,modal:true,draggable:false,resizable:false,width:680,autoOpen:false});
		//$("#shipped-modal").dialog({height:250,modal:true,draggable:false,resizable:false,width:680,autoOpen:false});
		//$("#recieved-modal").dialog({height:250,modal:true,draggable:false,resizable:false,width:680,autoOpen:false});
		//$("#process-modal").dialog({height:650,modal:true,draggable:false,resizable:false,width:680,autoOpen:false});
		
		$("#placed-btn").click(function(){
			$("#placed-modal").dialog('open');
		});
		
		$("#secured-btn").click(function(){
			$("#secured-modal").dialog('open');
		});
		
		$("#warehouse-btn").click(function(){
			$("#warehouse-modal").dialog('open');
		});
		
		$("#shipped-btn").click(function(){
			$("#shipped-modal").dialog('open');
		});
		
		$("#recieved-btn").click(function(){
			$("#recieved-modal").dialog('open');
		});
		
		$("#process-btn").click(function(){
			$("#process-modal").dialog('open');
		});
		
		//tool tips
		$(".tip").tooltip();

		
	});
	
</script>
