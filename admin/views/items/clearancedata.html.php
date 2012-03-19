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
	<h2 id="page-heading">Backorder/Clearance Items</h2>
</div>

<div id="clear"></div>

<?php
//print_r($items_skus);
?>


<div id="clear"></div>
<div class="grid_16">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">Populate Backorder XLS with more item data</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<?php echo $this->form->create(); ?>

				<?php echo $this->form->field('ItemsSubmit', array('type' => 'textarea', 'rows' => '7', 'cols' => '50', 'name' => 'ItemsSubmit'));?><br>
	
				<?php echo $this->form->submit('Submit'); ?>
				(Search By: Description, Vendor, Vendor Style or SKU)
			<?php echo $this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>

<div id="clear"></div>
<div id="clear"></div>
<div class="grid_16">
<?php if (!empty($datas)): ?>

	<table>
		<thead>
			<tr>
				<?php foreach($heading as $head){ ?>
				<th><?php echo $head ?></th>
				<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($items_skus as $eachrow): 
			$data = $datas[$eachrow];
			?>
				<tr>
				<?php foreach ($data as $key => $value): ?>
					<td>
						<?php echo $value ?>
					</td>
				<?php endforeach ?>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>
