<?php $this->title("My Orders"); ?>
<h1 class="p-header">My Account</h1>
<?=$this->menu->render('left'); ?>

<div id="middle" class="noright">				
	
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		
		<div class="mar-10-b">
			<strong class="gray">Shipping Process</strong>&nbsp;|&nbsp;<a href="javascript:void(0)" id="process-btn" title="more information">more info</a>
		</div>
		
		<p>Note: Orders may be split into multiple shipments with different tracking numbers.</p>
		
		<ol class="shipping-process">
			<li class="placed link" id="placed-btn">Order Placed With Totsy</li>
			<li class="secured link" id="secured-btn">Order Secured From Partners</li>
			<li class="warehouse link" id="warehouse-btn">Items Arrive At Warehouse</li>
			<li class="shipped link" id="shipped-btn">Packaged Shipped To You</li>
			<li class="recieved link" id="recieved-btn">Order Arrives At Your Home</li>
		</ol>
		<?php if ($orders->data()): ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="order-table">
		
			<thead>
				<tr>
					<th width="10%">Order Date</th>
					<th width="10%">Order ID</th>
					<th width="40%">Items </th>
					<th width="20%">Status</th>
					<th width="30%">Tracking</th>
				</tr>
			</thead>
			
			<tbody>
				<?php foreach ($orders as $order): ?>
					<tr class="alt$x">
						<td><?=date('m-d-y', $order->date_created->sec); ?></td>
						<td>
							<span class="tip"><?php echo strtoupper(substr((string)$order->_id, 0, 8));	?></span>
						</td>
						<?php if ($order->items): ?>
							<?php $items = $order->items->data() ?>
						<?php endif ?>
						<td>
						<?php foreach ($items as $item): ?>
								<?=$item['description']?><br>
								Color: <?=$item['color']?><br>
								Size: <?=$item['size']?><br>
								Quantity: <?=$item['quantity']?><br><br>
						<?php endforeach ?>
						</td>
						<td>
							<?php foreach ($items as $item): ?>
									<?=$item['status']?><br>
							<?php endforeach ?>
						</td>
						<td></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		
		</table>
		<?php else: ?>
			<center><strong>You have have no orders.</strong></center>
		<?php endif ?>
	</div>
	<div class="bl"></div>
	<div class="br"></div>
	
</div>

</div>
	<div id="placed-modal" class="modal-div">
		<ol class="shipping-process">
			<li class="placed active">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="placed-header">Order <span>Placed</span> With Totsy</h3>
		<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		
	</div>
	
	<div id="secured-modal" class="modal-div">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured active">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="secured-header">Order Secured <span>From Partners</span></h3>
		<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		
	</div>
	
	<div id="warehouse-modal" class="modal-div">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse active">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="warehouse-header">Items Arrive <span>At Warehouse</span></h3>
		<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		
	</div>
	
	<div id="shipped-modal" class="modal-div">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped active">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="shipped-header">Package <span>Shipped</span> To You</h3>
		<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		
	</div>
	
	<div id="recieved-modal" class="modal-div">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved active">Order Arrives At Your Home</li>
		</ol>
		
		<h3 class="recieved-header">Order Arrives <span>At Your Home</span></h3>
		<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		
	</div>
	
	<div id="process-modal" class="modal-div">
		<ol class="shipping-process">
			<li class="placed">Order Placed With Totsy</li>
			<li class="secured">Order Secured From Partners</li>
			<li class="warehouse">Items Arrive At Warehouse</li>
			<li class="shipped">Packaged Shipped To You</li>
			<li class="recieved">Order Arrives At Your Home</li>
		</ol>
		
		<span class="red">Totsy Shipping Process</span>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia euismod. Ut hendrerit massa eu erat rutrum ut vulputate justo dignissim. Suspendisse sed nunc libero, non eleifend purus. Vivamus porttitor turpis nec purus commodo laoreet. Maecenas condimentum semper est eget bibendum. Nam sem est, tristique non aliquet in, scelerisque at lacus.</p>
		
		<div class="process-list-item">
			<h3 class="placed-header">Order <span>Placed</span> With Totsy</h3>
			<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		</div>
		
		<div class="process-list-item">
			<h3 class="secured-header">Order Secured <span>From Partners</span></h3>
			<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		</div>
		
		<div class="process-list-item">
			<h3 class="warehouse-header">Items Arrive <span>At Warehouse</span></h3>
			<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		</div>
		
		<div class="process-list-item">
			<h3 class="shipped-header">Package <span>Shipped</span> To You</h3>
			<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
		</div>
		
		<div class="process-list-item">
			<h3 class="recieved-header">Order Arrives <span>At Your Home</span></h3>
			<p class="modal-copy">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae dolor urna, sit amet sodales turpis. Ut justo ligula, fermentum in dapibus nec, sollicitudin nec arcu. Phasellus et tellus non lectus lacinia</p>
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
		$("#placed-modal").dialog({height:220,modal:true,draggable:false,resizable:false,width:654,autoOpen:false});
		$("#secured-modal").dialog({height:220,modal:true,draggable:false,resizable:false,width:654,autoOpen:false});
		$("#warehouse-modal").dialog({height:220,modal:true,draggable:false,resizable:false,width:654,autoOpen:false});
		$("#shipped-modal").dialog({height:220,modal:true,draggable:false,resizable:false,width:654,autoOpen:false});
		$("#recieved-modal").dialog({height:220,modal:true,draggable:false,resizable:false,width:654,autoOpen:false});
		$("#process-modal").dialog({height:600,modal:true,draggable:false,resizable:false,width:654,autoOpen:false});
		
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