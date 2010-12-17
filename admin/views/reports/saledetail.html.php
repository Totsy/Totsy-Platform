<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>


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
				<?=$this->form->create($data); ?>
						<p>
							<?=$this->form->label('Minimum Order Date:'); ?>
							<?=$this->form->text('min_date', array(
								'id' => 'min_date',
								'class' => 'date',
								'style' => 'width:100px; margin: 0px 40px 0px 0px;'
								));
							?>
						</p>
						<p>
							<?=$this->form->label('Maximum Order Date:'); ?>
							<?=$this->form->text('max_date', array(
								'id' => 'max_date',
								'class' => 'date',
								'style' => 'width:100px; margin: 0px 40px 0px 0px;'
								));
							?>
						</p>
						<p>
							<?=$this->form->label('State:'); ?>
							<?=$this->form->select('state', array(
								'All' => 'Show All States',
								'NY' => 'Limit to NY',
								'PA' => 'Limit to PA'),
								array('style' => 'width:150px; margin: 0px 20px 0px 0px;')
								);
							?>
						</p>
						<p>
							<?=$this->form->label('Include/Exclude Category:'); ?>
							<?=$this->form->select('include_category', array(
								1 => 'Include Apparel/Footwear Sales',
								0 => 'Exclude Apparel/Footware Sales'),
								array('style' => 'width:220px; margin: 0px 20px 0px 0px;')
								);
							?>
						</p>
						<p>
							
							<?=$this->form->label('Order Total:'); ?>
							<?=$this->form->select('range_type', array(
								'$gt' => 'Greater Than',
								'$lt' => 'Less Than'),
								array('style' => 'width:120px; margin: 0px 20px 0px 0px;')
								);
							?>
							$<?=$this->form->text('amount', array(
								'id' => 'amount',
								'class' => 'money',
								'style' => 'width:50px; margin: 0px 20px 0px 0px;'));
							?>
							<p>Leave blank if you want a $0 minimum</p>
						</p>
					<?=$this->form->submit('Search'); ?>
				<?=$this->form->end(); ?>
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
				<td><?=$result['date'] + 1;?></td>
				<td>$<?=number_format($result['gross'], 2);?></td>
				<td>$<?=number_format($result['handling'], 2);?></td>
				<td>$<?=number_format($result['tax'], 2);?></td>
				<td>$<?=number_format($result['sub_total'], 2);?></td>
				<td><?=$result['count'];?></td>
				<td><?=$result['state'];?></td>
				<td><?=number_format(($result['tax']/$result['sub_total'])*100, 2);?>%</td>
				<td>$<?=number_format($result['total'], 2);?></td>
				<td>$<?=-number_format($result['credit_used'], 2);?></td>
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