<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>

<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#min_date, #max_date').datetimepicker({
			defaultDate: "-2w",
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
	<h2 id="page-heading">Order Search</h2>
</div>
<div class="grid_4">
	<div class="box">
	<h2>
		<a href="#" id="toggle-forms">Date Search</a>
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
					</p>
				<?=$this->form->submit('Search'); ?>
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>

<?php if (!empty($orders)): ?>
	<table id="orderTable" class="datatable" border="1" style="width: 700px">
		<thead>
			<tr>
				<?php 
				foreach ($headings as $heading) {
					echo "<th>$heading</th>";
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($orders as $order): ?>
				<tr>
					<td><?=date('m-d-Y', $order['date_created']['sec']);?></td>
					<td>
						<?=$this->html->link($order['order_id'], array(
						'Orders::view',
						'args'=>$order['order_id']),
						array('target' => '_blank')); 
						?>
					</td>
					<td>
						<div>
						<?=$order['billing']['firstname']?>
						<?=$order['billing']['lastname']?><br>
						<?=$order['billing']['address']?>
						<?=$order['billing']['city']?> <?=$order['billing']['state']?> <?=$order['billing']['zip']?>
						</div>
					</td>
					<td>
						<?=$order['shipping']['firstname']?>
						<?=$order['shipping']['lastname']?><br>
						<?=$order['shipping']['address']?>
						<?=$order['shipping']['city']?> <?=$order['shipping']['state']?> <?=$order['shipping']['zip']?>
					</td>
					<td>$<?=number_format($order['total'],2);?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#orderTable').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>
