<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->style('TableTools');?>

<style type="text/css">
	td {
		font-size:12px;
		text-align: center;
	}
	th {
		font-size:12px;
		text-align: center;
	}
	.negative{
		font-size:12px;
		color:#FF0000;
	}
</style>

<div class="grid_16">
	<h2 id="page-heading">Revenue by Month</h2>
	<?php echo $this->html->link('Daily Revenue', 'Revenue::daily')."&nbsp;&nbsp;"; ?>
	<?php echo $this->html->link('Promocode Revenue', 'Revenue::promocodes')."<br/>"; ?>
</div>
<!--Gross Revenue Summary Begins Here-->
<div class="clear"></div>

<div class="grid_16">
	<h2>
		Gross Revenue
	</h2>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php
	    
	?>
	<table id="gross_table" class="" border="1">
		<thead>
			<tr>
				<th>Month</th>
				<th>Product</th>
				<th>Shipping</th>
				<th>Tax</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($gross_revenue as $gross) {
				echo '<tr><td><b>'.$gross['month'].'</b></td>';
				echo '<td>'.number_format($gross['subTotal'],2, '.', '').'</td>';
				echo '<td>'.number_format($gross['handling_total'],2, '.', '').'</td>';
				echo '<td>'.number_format($gross['tax'],2, '.', '').'</td>';
				echo '<td><b>'.number_format($gross['total'],2, '.', '').'</b></td></tr>';
			}
			
			?>
		</tbody>
	</table>
</div>

<!--Net Revenue Summary Begins Here-->
<div class="clear"></div>
<div class="grid_16">
	<h2>
		Net Revenue
	</h2>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php
	?>
	<table id="net_table" class="" border="1">
		<thead>
			<tr>
				<th>Month</th>
				<th>Promotions</th>
				<th>10 off 50<br/>Service</th>
				<th>Credits</th>
				<th>Free Shipping<br/>Service</th>
				<th>Free Shipping<br/>Promo Codes</th>
				<th>Product</th>
				<th>Shipping</th>
				<th>Tax</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($net_revenue as $net) {
				echo '<tr><td><b>'.$net['month'].'</b></td>';
				echo '<td><font class="negative">'.number_format($net['promo_discount'],2, '.', '').'</font></td>';
				echo '<td><font class="negative">'.number_format($net['discount'],2, '.', '').'</font></td>';
				echo '<td><font class="negative">'.number_format($net['credit_used'],2, '.', '').'</font></td>';
				echo '<td><font class="negative">'.-number_format($net['fs_service'],2, '.', '').'</font></td>';
				echo '<td><font class="negative">'.-number_format($net['fs_promo'],2, '.', '').'</font></td>';
				echo '<td>'.number_format($net['product'],2, '.', '').'</td>';
				echo '<td>'.number_format($net['handling_total'],2, '.', '').'</td>';
				echo '<td>'.number_format($net['tax'],2, '.', '').'</td>';
				echo '<td><b>'.number_format($net['total'],2, '.', '').'</b></td></tr>';
			}
			?>
		</tbody>
	</table>
</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#gross_table').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": false,
			"bFilter": false
		}
		);
		$('#net_table').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": false,
			"bFilter": false
		}
		);
	} );
</script>