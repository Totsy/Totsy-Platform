<?php $this->title("My Orders"); ?>
<?=$this->menu->render('left', array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'))); ?>

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
		
		<h2 class="gray mar-b">My Orders</h2>

		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="order-table">
		
			<thead>
				<tr>
					<th width="10%">Order Date</th>
					<th width="20%">Order ID</th>
					<th width="40%">Items </th>
					<th width="20%">Status</th>
					<th width="30%">Tracking</th>
				</tr>
			</thead>
			
			<tbody>
				<tr class="alt0">
					<td>04-12-10</td>
					<td>

						<span class="tip">3434544</span>
						<div class="tooltip">
							<strong class="caps red">Order Placed With Totsy</strong><br />
							Your order has been received and we are in the process of verifying all the information on your order. We will contact you if anything is wrong with your order or if we need more information. An email confirmation of your order is sent immediately to your inbox.
						</div>
						
					</td>
					<td>
						Standard Issue Kangaroo Pocket Knit Sweater<br />
						Color: Gray<br />
						Size: S<br />
						Quantity: 1
					</td>
					<td>Order Placed</td>
					<td>
						<p>Your order has shipped on 04-12-10 Via UPS Ground</p>
					</td>
				</tr>
				
				<tr class="alt1">
					<td>04-12-10</td>
					<td>
						
						<span class="tip">3434544</span>
						<div class="tooltip">
							<strong class="caps red">Order Placed With Totsy</strong><br />
							Your order has been received and we are in the process of verifying all the information on your order. We will contact you if anything is wrong with your order or if we need more information. An email confirmation of your order is sent immediately to your inbox.
						</div>
						
					</td>
					<td>
						Standard Issue Kangaroo Pocket Knit Sweater<br />
						Color: Gray<br />
						Size: S<br />
						Quantity: 1
					</td>
					<td>Order Placed</td>
					<td>
						<p>Your order has shipped on 04-12-10 Via UPS Ground</p>
					</td>
				</tr>
				
			</tbody>
		
		</table>
	
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
<script type="text/javascript" src="../js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="../js/jquery.equalheights.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.2.custom.min.js"></script>
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