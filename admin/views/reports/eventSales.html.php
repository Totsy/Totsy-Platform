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
	<h2 id="page-heading">Reports - Event Sales</h2>
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
	<?php if (!empty($dates)): ?>
		<h3>Showing Events Running from <?=$dates['min_date']?> GMT to <?=$dates['max_date']?> GMT</h3>
	<?php endif ?>
	<?php if (!empty($total)): ?>
		<table id="total_table" class="datatable" border="1">
			<thead>
				<tr>
					<th>Event</th>
					<th># of Units</th>
					<th>Total Sale Retail</th>
				</tr>
			</thead>
			<tr>
				<td>All Events</td>
				<td><?=$total['quantity'];?></td>
				<td>$<?=number_format($total['total'], 2);?></td>
			</tr>
	<?php endif ?>
	<?php if (!empty($results)): ?>
		<table id="summary_table" class="datatable" border="1">
			<thead>
				<tr>
					<th>Event</th>
					<th># of Units</th>
					<th>Total Sale Retail</th>
				</tr>
			</thead>
		<?php foreach ($results as $result): ?>
			<tr>
				<td>
			<?=$this->html->link($result["event"],'reports/event/'.$result["id"]."/".strtotime($dates['min_date'])."/".strtotime($dates['max_date']))?>
				</td>
				<td><?=$result['quantity'];?></td>
				<td><?=number_format($result['total'], 2, '.', ''));?></td>
			</tr>
		<?php endforeach ?>
	<?php endif ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#summary_table').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": false
		}
		);
	} );
</script>