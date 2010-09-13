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
	<h2 id="page-heading">Order Management</h2>
</div>
<div class="grid_4">
	<?php echo $this->view()->render(array('element' => '../elements/box'), array(
		'boxtitle' => 'Searching',
		'boxbody' => "Search for orders using either date, order id or user information."
	)); ?>
</div>
<div id="clear"></div>
<div class="grid_3">
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
					<?=$this->form->hidden('type', array('value' => 'date')); ?>
				<?=$this->form->submit('Search'); ?>
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>
<div id="clear"></div>
<div class="grid_3">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">Order Search</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<?=$this->form->create(); ?>
					<p>
						<?=$this->form->label('Order Number'); ?>
						<?=$this->form->text('order_id', array('id' => 'order_id'));?>
					</p>
					<?=$this->form->hidden('type', array('value' => 'order')); ?>
				<?=$this->form->submit('Search'); ?>
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>
<div id="clear"></div>
<div class="grid_6">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">User Search</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<?=$this->form->create(); ?>
					<p>
						<?=$this->form->label('First Name'); ?>
						<?=$this->form->text('firstname', array('id' => 'firstname'));?>
					</p>
					<p>
						<?=$this->form->label('Last Name'); ?>
						<?=$this->form->text('lastname', array('id' => 'lastname'));?>
					</p>
					<p>
						<?=$this->form->label('Email'); ?>
						<?=$this->form->text('email', array('id' => 'email'));?>
					</p>
					<p>
						<?=$this->form->label('Address'); ?>
						<?=$this->form->text('address', array('id' => 'address'));?>
					</p>
					<p>
						<?=$this->form->label('Lookup Type'); ?>
						<?=$this->form->select('address_type', array('Billing' => 'Billing', 'Shipping' => 'Shipping')); ?>
					</p>
					<?=$this->form->hidden('type', array('value' => 'user')); ?>
				<?=$this->form->submit('Search'); ?>
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>
<div id="clear"></div>
<div class="grid_16">
<?php if (!empty($orders)): ?>
	<table id="orderTable" class="datatable" border="1" style="width: 800px">
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
						<?php if (!empty($order['billing'])): ?>
							<?=$order['billing']['firstname']?>
							<?=$order['billing']['lastname']?><br>
							<?=$order['billing']['address']?>
							<?=$order['billing']['city']?> <?=$order['billing']['state']?> <?=$order['billing']['zip']?>
						<?php endif ?>
						</div>
					</td>
					<td>
						<?php if (!empty($order['shipping'])): ?>
							<?=$order['shipping']['firstname']?>
							<?=$order['shipping']['lastname']?><br>
							<?=$order['shipping']['address']?>
							<?=$order['shipping']['city']?> <?=$order['shipping']['state']?> <?=$order['shipping']['zip']?>
						<?php endif ?>
					</td>
					<td>$<?=number_format($order['total'],2);?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#orderTable').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>
