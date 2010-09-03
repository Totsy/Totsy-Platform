<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('table');?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>

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
<?=$this->form->create(); ?>
	<div id="order_date">
		<h2 id="order_date">Order Place</h2>
		<?=$this->form->field('min_date', array('class' => 'general', 'id' => 'min_date'));?>
		<?=$this->form->field('max_date', array('class' => 'general', 'id' => 'max_date'));?>
	</div>
	<?=$this->form->submit('Search'); ?>
<?=$this->form->end(); ?>


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
