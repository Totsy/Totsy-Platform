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
				<?php echo $this->form->create(); ?>
						<p>
							<?php echo $this->form->label('Minimum Order Date'); ?>
							<?php echo $this->form->text('min_date', array('id' => 'min_date'));?>
						</p>
						<p>
						<?php echo $this->form->label('Maxium Order Date'); ?>
						<?php echo $this->form->text('max_date', array('id' => 'max_date'));?>
					<?php echo $this->form->submit('Search'); ?>
				<?php echo $this->form->end(); ?>
			</fieldset>
		</div>
	</div>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php if (!empty($dates)): ?>
		<h3>Showing Events Running from <?php echo $dates['min_date']?> GMT to <?php echo $dates['max_date']?> GMT</h3>
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
				<td><?php echo $total['quantity'];?></td>
				<td>$<?php echo number_format($total['total'], 2);?></td>
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
			<?php echo $this->html->link($result["event"],'reports/event/'.$result["id"]."/".strtotime($dates['min_date'])."/".strtotime($dates['max_date']))?>
				</td>
				<td><?php echo $result['quantity'];?></td>
				<td><?php echo number_format($result['total'], 2, '.', '');?></td>
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