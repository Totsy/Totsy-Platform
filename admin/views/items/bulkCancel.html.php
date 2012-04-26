<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>

<script>
function confirmSubmit() {
	var agree = confirm("Are you sure you wish to continue?");
	if (agree) {
		return true ;
	} else {
		return false ;
	}
}
</script>

<div class="grid_16">
	<h2 id="page-heading">
		Bulk Cancellation Tool
		<?php if ($search_sku): ?>
			- Searching for SKU <?php echo $search_sku;?>
		<?php endif; ?>
	</h2>
</div>

<div  class="grid_16">
<p>This bulk cancel tool shows you all orders for a particular item or line item.   You can sort by most recent orders and cancel only those orders that have either been short-shipped or marked as unreceivable by the warehouse.  When the line-item is canceled, the user will receive an email notification letting them know the line-item was canceled from their order.
</p></div>

<div id="clear"></div>


<div id="clear"></div>
<div class="grid_16">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">Search</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<?php echo $this->form->create(); ?>
				<?php echo $this->form->text('search', array(
					'id' => 'search',
					'style' => 'float:left; width:440px; margin: 0px 10px 0px 0px;'
					));
				?>
				<?php echo $this->form->submit('Submit'); ?>
				(Search By SKU only)
			<?php echo $this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>

<div id="clear"></div>
<div id="clear"></div>

<div class="grid_16">
<?php if (!empty($items)): ?>
	<?php
		$x=0;
		foreach ($items as $item) {
	?>
	<table border="1">
	<thead>
		<tr>
			<th>Image</th>
			<th>Sale Retail</th>
			<th>MSRP</th>
			<th>Description</th>
			<th>Vendor</th>
			<th>Vendor Style</th>
			<th>Color</th>
			<th>Size</th>
			<th>Totsy SKU</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<?php
				if (!empty($item['primary_image'])) {
					$image = '/image/'. $item['primary_image'] . '.jpeg';
				} else {
					$image = "/img/no-image-small.jpeg";
				}
			?>
			<td width="5%">
				<?php echo $this->html->image("$image", array(
					'width' => "110",
					'height' => "110",
					'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;"
					));
				?>
			</td>
			<td>$<?php echo $item['sale_retail']?></td>
			<td>$<?php echo $item['msrp']?></td>
			<td width="5%"><?php echo $item['description']?></td>
			<td><?php echo $item['vendor']?></td>
			<td width="5%"><?php echo $item['vendor_style']?></td>
			<td>
			<?php if (empty($item['color'])): ?>
				None
			<?php else: ?>
				<?php echo $item['color']?>
			<?php endif ?>
			</td>
			<td>
				<?php foreach ($item['sku_details'] as $key => $value): ?>
					<?php echo $key?><br />
				<?php endforeach ?>
			</td>
			<td>
				<?php foreach ($item['sku_details'] as $key => $value): ?>
					<a href="/items/bulkCancel/<?php echo $value;?>"><?php echo $value?></a><br />
				<?php endforeach ?>
			</td>
		</tr>
		<tr>
			<td colspan="9">
				<?php echo $this->form->create(null, array('url'=>'/orders/cancelMultipleItems')); ?>
					<?php echo $this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $item["_id"])); ?>
					<?php echo $this->form->hidden('sku', array('class' => 'inputbox', 'sku' => 'sku', 'value' => $search_sku)); ?>
						<table id="orderTable_<?php echo $x;?>" class="datatable" >
							<thead>
								<tr>
									<th><div class="controls">
										<span>
											<input type="checkbox" class="checkAll" />
										</span>
									</th>
									<th>Order ID</th>
									<th>Order Status</th>
									<th>SKU</th>
									<th>Firstname Lastname</th>
									<th>Quantity</th>
									<th>Size</th>
									<th>Color</th>
									<th>Order Date / Time</th>
									<th>Actions</th>
								</tr>
							</thead>
						<fieldset>
							<tbody>
<?php
$i = 0;
foreach($ordersForItem[(string) $item['_id']] as $order):
	$order_temp = $order->data();								
	$o = 0;
	while ($o < sizeof($order_temp["items"])) {
		if ($order_temp["items"][$o]["item_id"] == $item['_id']) {
			$line_item = $order_temp["items"][$o];
			$o = 99; //break out of loop
		}
		$o++;
	}
	foreach ($item['sku_details'] as $key => $value) {
		if ($key == $line_item["size"]) {
			$sku = $value;
		}
	}
	if ($sku == $search_sku) : ?>
		<tr>
			<td>
			<div style="display: none;">
				<input type="type" id="<?php echo $order[_id];?>" name="order[<?php echo $i;?>]" value="" class="cb-element" >
				<input type="hidden" id="line_number[<?php echo $i;?>]" name="line_number[<?php echo $i;?>]" value="<?php echo $line_item[line_number];?>">
	
			</div>
			<?php if ($order->auth_confirmation <= -1) : ?>
				<?php if (!$line_item["cancel"] || $line_item["cancel"] == 0): ?>
	<input type="checkbox" id="<?php echo $order['_id']; ?>" name="order[<?php echo $i; ?>]" value="<?php echo $order['_id']; ?>" class="cb-element" >
	<input type="hidden" id="line_number[<?php echo $i; ?>]" name="line_number[<?php echo $i; ?>]" value="<?php echo $line_item['line_number']; ?>">
				<?php endif; ?>
			<?php endif; ?>
			</td>
			<td><?php echo $order["order_id"];?></td>
			<td><?php
			    if ($line_item["cancel"] == 1): ?>
			    <strong>Cancelled</strong>
			    <?php else:?>
			        <?php echo $line_item["status"];?>
			    <?php endif; ?></td>
			<td><?php echo $sku;?></td>
			<td><?php echo $order->billing->firstname." ".$order->billing->lastname;?></td>
			<td><?php echo $line_item["quantity"];?></td>
			<td><?php echo $line_item["size"];?></td>
			<td>
			<?php if (empty($line_item['color'])): ?>
				None
			<?php else: ?>
				<?php echo $line_item['color']?>
			<?php endif; ?>
			</td>
			<td><?php echo date('Y-M-d h:i:s',$order_temp[date_created]);?></td>
			<td>
			<a href="/orders/view/<?php echo $order[_id];?>">View Order</a>
			<?php if ($order->auth_confirmation <= -1) : ?>
			<?php if ($line_item[cancel] != 1) {?> | <a href="/orders/cancelOneItem?line_number=<?php echo $line_item[line_number];?>&order_id=<?php echo $order[_id];?>&item_id=<?php echo (string) $item['_id'];?>&sku=<?php echo $sku;?>" onclick="return cancelLineItem();">Cancel</a><?php } ?>
			<?php endif; ?>
			</td>
		</tr>
<?php
		$i++;
	endif; //end of search for sku
endforeach; //end of orders TR
?>
							</tbody>
							</fieldset>
						</table>
						<!-- end of orders table within items table -->

					</td>
				</tr>



		</tbody>
	</table>

					<br/>		<input type="submit" id="submit"  value="Bulk Cancel these Line Items" onClick="return confirmSubmit()"/>
						<?php echo $this->form->end();?>
<br/><br/>
<hr/>

			<?php
			$x++;

			}  //end of items foreach ?>
	<!-- end of items table -->
<?php  endif ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";

		<?php
			$a=0;
			while ($a < $x) {
		?>
		$('#orderTable_<?php echo $a;?>').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"aaSorting": [[ 8, "desc" ]],
			"bStateSave": true,
			"aoColumnDefs": [ {"bSortable": false, "aTargets": [0] } ]
		});

		<?php
				$a++;
			}
		?>

	}
	);

</script>

<script type="text/javascript" language="javascript">
	$( function() {
		$( '.checkAll' ).live( 'change', function() {
			$( '.cb-element' ).attr( 'checked', $( this ).is( ':checked' ) ? 'checked' : '' );
			$( this ).next().text( $( this ).is( ':checked' ) ? 'Uncheck All' : 'Check All' );
		});
		$( '.invertSelection' ).live( 'click', function() {
			$( '.cb-element' ).each( function() {
				$( this ).attr( 'checked', $( this ).is( ':checked' ) ? '' : 'checked' );
			}).trigger( 'change' );

		});
		$( '.cb-element' ).live( 'change', function() {
			$( '.cb-element' ).length == $( '.cb-element:checked' ).length ? $( '.checkAll' ).attr( 'checked', 'checked' ).next().text( 'Uncheck All' ) : $( '.checkAll' ).attr( 'checked', '' ).next().text( 'Check All' );

		});
	});
</script>