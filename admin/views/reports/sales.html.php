<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>

<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#min_date, #max_date').datetimepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == "min_date" ? "minDate" : "maxDate";
				var instance = $(this).data("datetimepicker");
				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
			}
		});
	});
</script>
<div class="grid_16">
	<h2 id="page-heading">Reports - Sales</h2>
</div>
<div class="clear"></div>
<div class="grid_6">
	<div class="box">
		<h2>
			<p>Search By Date</p>
		</h2>
		<div class="block" id="forms">
			<fieldset>
				<?=$this->form->create(); ?>
						<p>
							<?=$this->form->label('Minimum Order Date'); ?>
							<?=$this->form->text('min_date', array('id' => 'min_date'));?>
						</p>
						<p>
						<?=$this->form->label('Maxium Order Date'); ?>
						<?=$this->form->text('max_date', array('id' => 'max_date'));?>
					<?=$this->form->submit('Search'); ?>
				<?=$this->form->end(); ?>
			</fieldset>
		</div>
	</div>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php if (!empty($total)): ?>
		<table id="total_table" class="datatable" border="1">
			<thead>
				<tr>
					<th>Date</th>
					<th># of Orders</th>
					<th># of Units</th>
					<th>Tax</th>
					<th>Freight</th>
					<th>Total</th>
				</tr>
			</thead>
			<tr>
				<td>Total (<?=$dates['min_date'];?> through <?=$dates['max_date']?>)</td>
				<td><?=$total['count'];?></td>
				<td><?=$total['quantity'];?></td>
				<td>$<?=number_format($total['tax'], 2);?></td>
				<td>$<?=number_format($total['handling'], 2);?></td>
				<td>$<?=number_format($total['total'], 2);?></td>
			</tr>
	<?php endif ?>
	<?php if (!empty($summary)): ?>
		<table id="summary_table" class="datatable" border="1">
			<thead>
				<tr>
					<th>Date</th>
					<th># of Orders</th>
					<th># of Units</th>
					<th>Tax</th>
					<th>Freight</th>
					<th>Total</th>
				</tr>
			</thead>
		<?php foreach ($summary as $result): ?>
			<tr>
				<td><?=$result['date'];?></td>
				<td><?=$result['count'];?></td>
				<td><?=$result['quantity'];?></td>
				<td>$<?=number_format($result['tax'], 2);?></td>
				<td>$<?=number_format($result['handling'], 2);?></td>
				<td>$<?=number_format($result['total'], 2);?></td>
			</tr>
		<?php endforeach ?>
		</table>
	<?php endif ?>
	<?php if (!empty($details)): ?>
		<table id="results_table" class="datatable" border="1">
			<thead>
				<tr>
					<th>Date</th>
					<th>State</th>
					<th># of Orders</th>
					<th># of Units</th>
					<th>Tax</th>
					<th>Freight</th>
					<th>Total</th>
				</tr>
			</thead>
		<?php foreach ($details as $result): ?>
			<tr>
				<td><?=$result['date'];?></td>
				<td><?=$result['state'];?></td>
				<td><?=$result['count'];?></td>
				<td><?=$result['quantity'];?></td>
				<td>$<?=number_format($result['tax'], 2);?></td>
				<td>$<?=number_format($result['handling'], 2);?></td>
				<td>$<?=number_format($result['total'], 2);?></td>
			</tr>
		<?php endforeach ?>
	<?php endif ?>
		</table>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#results_table').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": false,
			"bFilter": false
		}
		);
	} );
</script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#summary_table').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": false,
			"bFilter": false
		}
		);
	} );
</script>