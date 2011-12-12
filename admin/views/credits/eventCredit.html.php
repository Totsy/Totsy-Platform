<?php use admin\models\Credit; ?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Credit Management by Event - <?php echo $event->name?></h2>
</div>
<div class="grid_6">
	<div class="box">
		<h2>
			<a href="#" id="toggle-form">Apply Credits</a>
		</h2>

			<div class="block" id="forms">
				<?php echo $this->form->create(null); ?>
				<p>
					<?php echo $this->form->label('Reason For Credit: '); ?>
					<?php echo $this->form->select('reason', Credit::$reasons); ?>
				</p>
				<p>
					<?php echo $this->form->label('Credit Amount: '); ?>
					<?php echo $this->form->select('sign', array('+' => '+', '-' => '-')); ?>
					$<?php echo $this->form->text('amount', array('size' => 6)); ?>
				</p>
				<p>
					<?php echo $this->form->label('Description:'); ?>
					<?php echo $this->form->textarea('description'); ?>
				</p>
					<?php echo $this->form->submit('Apply'); ?>
				<?php echo $this->form->end(); ?>
				</div>
			</div>
		</div>
<div id="clear"></div>
<div class="grid_10">
	<div class="box">
		<h2>
			<a href="#" id="toggle-tables">Credit History</a>
		</h2>
		<div class="block" id="tables">
		<?php if (!empty($appliedCredit)): ?>
			<table id="creditTable" class="datatable" border="1">
				<thead>
					<tr>
						<th>Date</th>
						<th>Order Id</th>
						<th>Reason</th>
						<th>Description</th>
						<th>Amount</th>
						<th>Customer</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($appliedCredit as $credit): ?>
						<tr>
							<td>
								<?php if (!empty($credit->date_created->sec)): ?>
									<?php echo date('m-d-Y', $credit->date_created->sec);?>
								<?php else: ?>
									<?php echo date('m-d-Y', $credit->created->sec);?>
								<?php endif ?>
							</td>
							<td>
								<?php echo $this->html->link($credit->order_number, array(
								'Orders::view',
								'args' => $credit->order_id),
								array('target' => '_blank')); 
								?>
							</td>
							<td>
								<?php if ($credit->reason): ?>
									<?php echo $credit->reason;?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($credit->description): ?>
									<?php echo $credit->description;?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($credit->amount): ?>
									$<?php echo number_format($credit->amount, 2);?>
								<?php else: ?>
								$<?php echo number_format($credit->credit_amount, 2);?>
								<?php endif ?>
							</td>
							<td>
								<?php echo $this->html->link('View', array(
								'Users::view',
								'args'=> $credit->user_id),
								array('target' => '_blank'));
								?>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>
		</div>
	</div>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#creditTable').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>