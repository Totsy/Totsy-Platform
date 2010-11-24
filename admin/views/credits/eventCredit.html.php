<?php use admin\models\Credit; ?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Credit Management by Event - <?=$event->name?></h2>
</div>
<div class="grid_6">
	<div class="box">
		<h2>
			<a href="#" id="toggle-form">Apply Credits</a>
		</h2>

			<div class="block" id="forms">
				<?=$this->form->create(null); ?>
				<p>
					<?=$this->form->label('Reason For Credit: '); ?>
					<?=$this->form->select('reason', Credit::$reasons); ?>
				</p>
				<p>
					<?=$this->form->label('Credit Amount: '); ?>
					<?=$this->form->select('sign', array('+' => '+', '-' => '-')); ?>
					$<?=$this->form->text('amount', array('size' => 6)); ?>
				</p>
				<p>
					<?=$this->form->label('Description:'); ?>
					<?=$this->form->textarea('description'); ?>
				</p>
					<?=$this->form->submit('Apply'); ?>
				<?=$this->form->end(); ?>
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
									<?=date('m-d-Y', $credit->date_created->sec);?>
								<?php else: ?>
									<?=date('m-d-Y', $credit->created->sec);?>
								<?php endif ?>
							</td>
							<td>
								<?=$this->html->link($credit->order_number, array(
								'Orders::view',
								'args' => $credit->order_id),
								array('target' => '_blank')); 
								?>
							</td>
							<td>
								<?php if ($credit->reason): ?>
									<?=$credit->reason;?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($credit->description): ?>
									<?=$credit->description;?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($credit->amount): ?>
									$<?=number_format($credit->amount, 2);?>
								<?php else: ?>
								$<?=number_format($credit->credit_amount, 2);?>
								<?php endif ?>
							</td>
							<td>
								<?=$this->html->link('View', array(
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