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
	<h2 id="page-heading">Item Search</h2>
</div>

<div id="clear"></div>


<div id="clear"></div>
<div class="grid_16">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">Search</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<?=$this->form->create(); ?>
				<?=$this->form->text('search', array(
					'id' => 'search',
					'style' => 'float:left; width:400px; margin: 0px 10px 0px 0px;'
					));
				?>
				<?=$this->form->submit('Submit'); ?>
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>

<div id="clear"></div>
<div id="clear"></div>
<div class="grid_16">
<?php if (!empty($items)): ?>
	<table id="orderTable" class="datatable" border="1">
		<thead>
			<tr>
				<td>Image</td>
				<td>Description</td>
				<td>Vendor</td>
				<td>Vendor Style</td>
				<td>Color</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($items as $item): ?>
				<tr>
					<?php
						if (!empty($item['primary_image'])) {
							$image = '/image/'. $item['primary_image'] . '.jpg';
						} else {
							$image = "/img/no-image-small.jpeg";
						}
					?>
					<td style="padding:5px;" title="item">
						<?=$this->html->image("$image", array(
							'width' => "60",
							'height' => "60",
							'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;"
							));
						?>
					</td>
					<td><?=$item['description']?></td>
					<td><?=$item['vendor']?></td>
					<td><?=$item['vendor_style']?></td>
					<td>
					<?php if (empty($item['color'])): ?>
						None
					<?php else: ?>
						<?=$item['color']?>
					<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#orderTable').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>