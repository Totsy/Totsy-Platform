<?php
	use app\models\Menu;
	$this->title("My Orders");
	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	$doc = Menu::all(array('conditions' => array('location' => 'left', 'active' => 'true')));
?>
<?=$this->MenuList->build($doc, $options); ?>

<div class="tl"></div>
<div class="tr"></div>
<div id="page">

	<h2 class="gray mar-b"><?php echo ('Email Preferences'); ?></h2>

	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="order-table">

		<thead>
			<tr>
				<th width="10%"><?php echo ('Order Date');?></th>
				<th width="40%"><?php echo ('Items ');?></th>
				<th width="20%"><?php echo ('Order ID');?></th>
				<th width="30%"><?php echo ('Tracking');?></th>
			</tr>
		</thead>
		
		<tbody>
			<tr class="alt0">
				<td>04-12-10</td>
				<td>
					<a href="#" title="View product details">Standard Issue Kangaroo Pocket Knit Sweater</a><br />
					Color: Gray<br />
					Size: S<br />
					Quantity: 1
				</td>
				<td>
					<a href="#" title="View order number 34567890">34567890</a></td>
				<td>
					<p>Your order has shipped on 04-12-10 Via UPS Ground</p>
					<a href="#" title="track order number 34567890" class="flex-btn fr"><span><?php echo ('Track Shipment');?></span></a>
				</td>
			</tr>
			
			<tr class="alt1">
				<td>04-12-10</td>
				<td>
					<a href="#" title="View product details">Standard Issue Kangaroo Pocket Knit Sweater</a><br />
					Color: Gray<br />
					Size: S<br />
					Quantity: 1
				</td>
				<td>
					<a href="#" title="View order number 34567890">34567890</a></td>
				<td>
					<p>Your order has shipped on 04-12-10 Via UPS Ground</p>
					<a href="#" title="track order number 34567890" class="flex-btn fr"><span><?php echo ('Track Shipment');?></span></a>
				</td>
			</tr>
			
		</tbody>
	
	</table>
	
	
</div>
<div class="bl"></div>
<div class="br"></div>