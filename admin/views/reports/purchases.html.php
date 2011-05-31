<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('table');?>
<?=$this->html->style('TableTools');?>

<div class="grid_16">
	<h2 id="page-heading">Purchase Order: <?=$poNumber; ?>
	(<?=$event->name?>)</h2>
</div>
<div id="clear"></div>
<div class="grid_8">
	<div id="box">
		<?php if ($total['sum'] == 0 ): ?>
			<h2>No product has been sold for this event.</h2>
		<?php else: ?>
		<p>Total Quantity - <?=$total['quantity']?></p>
		<p>Order Total - $<?=number_format($total['sum'], 2)?></p>
		<?php endif ?>
	</div>
</div>
<?php if (!empty($purchaseOrder)): ?>
	<div class="grid_16">
			<table id="purchase_order" class="datatable" border="1">
				<thead>
					<tr>
						<?php
						foreach ($purchaseHeading as $heading) {
							echo "<th>$heading</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($purchaseOrder as $items): ?>
						<tr>
							<?php foreach ($items as $key => $value): ?>
								<?php if (in_array($key, array('Total', 'Unit'))): ?>
									<td>$<?=number_format($value, 2)?></td>
								<?php else: ?>
									<td><?=$value?></td>
								<?php endif ?>
							<?php endforeach ?>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
	</div>
<?php endif ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#purchase_order').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false
		}
		);
	} );
</script>