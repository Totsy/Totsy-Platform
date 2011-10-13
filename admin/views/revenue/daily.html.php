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
	<h2 id="page-heading">Revenue by Day</h2>
	<?php echo $this->html->link('Monthly Revenue', 'Revenue::monthly')."&nbsp;&nbsp;"; ?>
	<?php echo $this->html->link('Promocode Revenue', 'Revenue::promocodes')."<br/>"; ?>
</div>
<!--Gross Revenue Summary Begins Here-->
<div class="clear"></div>

<?=$this->form->create(null, array('enctype' => "multipart/form-data")); ?>
	<div>
		<?=$this->form->field('start_date', array('class' => 'general', 'id' => 'start_date'));?>
		<?=$this->form->hidden('request_start_date',array('value' => $start_date));?>
	</div>
	<br/>
	<div>
		<?=$this->form->field('end_date', array('class' => 'general', 'id' => 'end_date'));?>
		<?=$this->form->hidden('request_end_date',array('value' => $end_date));?>
		(results from the end date will be included, select the same start date and end date to see data for a single day)
	</div>
	<br/>
	<?=$this->form->submit('Update')?>
<?=$this->form->end(); ?>

<div class="clear"></div>	
<br/>
<hr/>

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
				<th>Date</th>
				<th>Product</th>
				<th>Shipping</th>
				<th>Tax</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($gross_revenue as $gross) {
				echo '<tr><td><b>'.$gross['date_string'].'</b></td>';
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
<br />
<hr />
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
				<th>Date</th>
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
				echo '<tr><td><b>'.$net['date_string'].'</b></td>';
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
	$(function() {
		$('#start_date').datepicker({
		});
		$('#end_date').datepicker({
		});
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
		$('#start_date').datepicker('setDate', new Date($('input[name=request_start_date]').val()));
		$('#end_date').datepicker('setDate', new Date($('input[name=request_end_date]').val()));
	});
</script>