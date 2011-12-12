<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>

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
	<h2 id="page-heading">User Management</h2>
</div>
<!-- <div id="clear"></div>
<div class="grid_6">
	<div class="box">
		<h2>
			<a href="#" id="toggle-order-search">Stuff</a>
		</h2>
		<div class="block">
		</div>
	</div>
</div> -->
<div id="clear"></div>
<div class="grid_6">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">User Search</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<?php echo $this->form->create(); ?>
					<p>
						<?php echo $this->form->label('First Name'); ?>
						<?php echo $this->form->text('firstname', array('id' => 'firstname'));?>
					</p>
					<p>
						<?php echo $this->form->label('Last Name'); ?>
						<?php echo $this->form->text('lastname', array('id' => 'lastname'));?>
					</p>
					<p>
						<?php echo $this->form->label('Email'); ?>
						<?php echo $this->form->text('email', array('id' => 'email'));?>
					</p>
					<p>
						<?php echo $this->form->label('Zip/Postal Code'); ?>
						<?php echo $this->form->text('zip', array('id' => 'zip'));?>
					</p>
				<?php echo $this->form->submit('Search'); ?>
			<?php echo $this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>
<div id="clear"></div>
<div class="grid_10">
	<div class="box">
		<h2>
			<a href="#" id="toggle-order-search">Users</a>
		</h2>
	<?php if (!empty($users)): ?>
		<table id="orderTable" class="datatable" border="1">
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
				<?php $inc = 0; ?>
				<?php foreach ($users as $user): ?>
					<?php ++$inc; ?>
					<tr>
						<td><?php echo $inc;?></td>
						<td>
							<?php echo $this->html->link($user->firstname, array(
							'Users::view',
							'args'=>$user->_id),
							array('target' => '_blank'));
							?>
						</td>
						<td>
							<?php echo $this->html->link($user->lastname, array(
							'Users::view',
							'args'=>$user->_id),
							array('target' => '_blank'));
							?>
						</td>
						<td>
							<?php echo $this->html->link($user->email, array(
							'Users::view',
							'args'=>$user->_id),
							array('target' => '_blank'));
							?>
						</td>
						<td>
							<?php echo $this->html->link($user->zip, array(
							'Users::view',
							'args'=>$user->_id),
							array('target' => '_blank'));
							?>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php else: ?>
		<center><p>----------------Please search for users----------------</p></center>
	<?php endif ?>
	</div>
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
