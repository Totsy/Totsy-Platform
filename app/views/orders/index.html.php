<?php $this->title("My Orders"); ?>
	<h1 class="p-header">My Account</h1>
	<div id="left">
		<ul class="menu main-nav">
			<li class="firstitem17"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
			<li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
			<li class="item18"><a href="/account/password" title="Change Password"><span>Change Password</span></a></li>
			<li class="item19"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
			<li class="item20 active"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
			<li class="item20"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
			<li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
			<br />
			<h3 style="color:#999;">Need Help?</h3>
			<hr />
			<li class="first item18"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
			<li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
		</ul>
	</div>

<div id="middle" class="noright">				
	
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
	<h2 class="gray mar-b">My Orders</h2>
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
								<strong><?=$item['description']?></strong><br />
								<span style="font-size:12px;">Color: <?=$item['color']?></span><br />
								<span style="font-size:12px;">Size: <?=$item['size']?></span><br />
								<span style="font-size:12px;">Quantity: <?=$item['quantity']?></span><br />
						<?php endforeach ?>
						</td>
						<td>
							<?php if (!empty($trackingNumbers) || !empty($order->tracking_numbers)): ?>
								Tracking Number(s):
								<?php if ($trackingNumbers): ?>
										<?php foreach ($trackingNumbers["$order->_id"] as $trackingNumber): ?>
											<?=$this->shipment->link($trackingNumber, array('type' => 'UPS'))?>
										<?php endforeach ?>
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
				<?php endforeach ?>
			</tbody>
		
		</table>
		<?php else: ?>
			<center><strong>You do not have any orders. <a href="/" title="Go Shopping">Go Shopping</a></strong></center>
		<?php endif ?>
	</div>
	<div class="bl"></div>
	<div class="br"></div>
	
</div>

</div>
	<div id="placed-modal" class="modal-div" style="display:none">
		<ol class="shipping-process">
			<li class="placed active">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="placed-header">Order <span>Placed</span> With Totsy</h3>
		<p class="modal-copy">Your order has been received and we are in the process of verifying all the information 
		relating to your transaction. We will contact you if anything is wrong with your order or if we need more information. 
		An email confirmation of your order has been sent to you.</p>
		
	</div>
	
	<div id="secured-modal" class="modal-div" style="display:none">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured active">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="secured-header">Order Secured <span>From Partners</span></h3>
		<p class="modal-copy">We have secured the product from the brand and 
		are awaiting a shipment from them. We will then process the items relating to this sale.</p>
		
	</div>
	
	<div id="warehouse-modal" class="modal-div" style="display:none">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse active">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="warehouse-header">Items Arrive <span>At Warehouse</span></h3>
		<p class="modal-copy">The merchandise from the completed sale is shipped directly to our warehouse, 
		where we prepare the products for shipment directly to you. This step allows us to inspect the merchandise so 
		it meets our stringent quality standards.</p>
		
	</div>
	
	<div id="shipped-modal" class="modal-div" style="display:none">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped active">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="shipped-header">Package <span>Shipped</span> To You</h3>
		<p class="modal-copy">The items for your order are shipped via one of our national carriers. 
		We try to consolidate all your items so you receive only one shipment; however, sometimes multiple shipments are unavoidable.
		 This process is greener and minimizes the wait time for orders that include items from sales running on overlapping dates.</p>
		
	</div>
	
	<div id="recieved-modal" class="modal-div" style="display:none">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved active">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="recieved-header">Order Arrives <span>At Your Home</span></h3>
		<p class="modal-copy">Your package has been delivered to your shipping address. Enjoy!</p>
		
	</div>
	
	<div id="process-modal" class="modal-div" style="display:none">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<span class="red">Totsy Shipping Process</span>
		<p>Totsy provides its members with incredible deals on top quality brands and products. In order to continue to provide deep discounts, we do not hold inventory, which allows us to drastically lower our costs and pass the savings on to our members. We work very closely with our partners to make sure our product inventory is reserved; however, they don't ship the product to our warehouse until after the event has ended. This causes a natural lag from the time you place your order to the time your items ship from Totsy. The whole process normally takes 3 to 4 weeks on average, and we're taking steps to make that duration shorter. For more information, please see the details below about each step.</p>
		
		<div class="process-list-item">
			<h3 class="placed-header">Order <span>Placed</span> With Totsy</h3>
			<p class="modal-copy">Your order has been received and we are in the process of verifying all the information 
		relating to your transaction. We will contact you if anything is wrong with your order or if we need more information. 
		An email confirmation of your order has been sent to you.</p>
		</div>
		
		<div class="process-list-item">
			<h3 class="secured-header">Order Secured <span>From Partners</span></h3>
			<p class="modal-copy">We have secured the product from the brand and 
		are awaiting a shipment from them. We will then process the items relating to this sale.</p>
		</div>
		
		<div class="process-list-item">
			<h3 class="warehouse-header">Items Arrive <span>At Warehouse</span></h3>
			<p class="modal-copy">The merchandise from the completed sale is shipped directly to our warehouse, 
		where we prepare the products for shipment directly to you. This step allows us to inspect the merchandise so 
		it meets our stringent quality standards.</p>
		</div>
		
		<div class="process-list-item">
			<h3 class="shipped-header">Package <span>Shipped</span> To You</h3>
			<p class="modal-copy">The items for your order are shipped via one of our national carriers. 
		We try to consolidate all your items so you receive only one shipment; however, sometimes multiple shipments are unavoidable.
		 This process is greener and minimizes the wait time for orders that include items from sales running on overlapping dates.</p>
		</div>
		
		<div class="process-list-item">
			<h3 class="recieved-header">Order Arrives <span>At Your Home</span></h3>
			<p class="modal-copy">Your package has been delivered to your shipping address. Enjoy!</p>
		</div>
		
	</div>
	
</div>
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
