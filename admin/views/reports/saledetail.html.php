<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>


<div class="grid_16">
	<h2 id="page-heading">Reports - Sale Details</h2>
</div>
<div class="clear"></div>
<div class="grid_10">
	<div class="box">
		<h2>
			<p>Query for Orders</p>
		</h2>
		<div class="block" id="forms">
			<fieldset>
				<?php echo $this->form->create($data); ?>
						<p>
							<?php echo $this->form->label('Minimum Order Date:'); ?>
							<?php echo $this->form->text('min_date', array(
								'id' => 'min_date',
								'class' => 'date',
								'style' => 'width:100px; margin: 0px 40px 0px 0px;'
								));
							?>
						</p>
						<p>
							<?php echo $this->form->label('Maximum Order Date:'); ?>
							<?php echo $this->form->text('max_date', array(
								'id' => 'max_date',
								'class' => 'date',
								'style' => 'width:100px; margin: 0px 40px 0px 0px;'
								));
							?>
						</p>
						<p>
							<?php echo $this->form->label('State:'); ?>
							<?php echo $this->form->select('state', array(
								'All' => 'Show All States',
								'NY' => 'Limit to NY',
								'PA' => 'Limit to PA'),
								array('style' => 'width:150px; margin: 0px 20px 0px 0px;')
								);
							?>
						</p>
						<p>
							<?php echo $this->form->label('Include/Exclude Category:'); ?>
							<?php echo $this->form->select('include_category', array(
								1 => 'Include Apparel/Footwear Sales',
								0 => 'Exclude Apparel/Footware Sales'),
								array('style' => 'width:220px; margin: 0px 20px 0px 0px;')
								);
							?>
						</p>
						<p>
							
							<?php echo $this->form->label('Order Total:'); ?>
							<?php echo $this->form->select('range_type', array(
								'$gt' => 'Greater Than',
								'$lt' => 'Less Than'),
								array('style' => 'width:120px; margin: 0px 20px 0px 0px;')
								);
							?>
							$<?php echo $this->form->text('amount', array(
								'id' => 'amount',
								'class' => 'money',
								'style' => 'width:50px; margin: 0px 20px 0px 0px;'));
							?>
							<p>Leave blank if you want a $0 minimum</p>
						</p>
					<?php echo $this->form->submit('Search'); ?>
				<?php echo $this->form->end(); ?>
			</fieldset>
		</div>
	</div>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php if (!empty($details)): ?>
		<table id="results_table" class="datatable" border="1">
			<thead>
				<tr>
					<th>Month</th>
					<th>Gross Sale</th>
					<th>Shipping</th>
					<th>Sales Tax</th>
					<th>Sub Total</th>
					<th>Net Sales</th>
					<th>Shipping State</th>
					<th>Sales Tax Rate</th>
					<th>Amount Paid</th>
					<th>Credit Used</th>
				</tr>
			</thead>
		<?php foreach ($details as $result): ?>
			<tr>
				<td><?php echo $result['date'] + 1;?></td>
				<td>$<?php echo number_format($result['gross'], 2);?></td>
				<td>$<?php echo number_format($result['handling'], 2);?></td>
				<td>$<?php echo number_format($result['tax'], 2);?></td>
				<td>$<?php echo number_format($result['sub_total'], 2);?></td>
				<td><?php echo $result['count'];?></td>
				<td><?php echo $result['state'];?></td>
				<td><?php echo number_format(($result['tax']/$result['sub_total'])*100, 2);?>%</td>
				<td>$<?php echo number_format($result['total'], 2);?></td>
				<td>$<?php echo -number_format($result['credit_used'], 2);?></td>
			</tr>
		<?php endforeach ?>
		</table>
	<?php endif ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#results_table').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": true,
			"bFilter": true
		}
		);
	} );
</script>
<script type="text/javascript">
jQuery(function($){
 	$(".date").mask("99/99/9999");
	$(".money").mask("999.99");
});
</script>