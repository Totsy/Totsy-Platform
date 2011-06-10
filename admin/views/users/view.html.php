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
	<h2 id="page-heading">User Management</h2>
</div>
<div id="clear"></div>
<div class="grid_6">
	<div class="box">
		<h2>
			<a href="#" id="toggle-tables">User Information</a>
		</h2>
		<div class="block" id="user-table">
			<table border="0" cellspacing="5" cellpadding="5" width="100">
				<?php foreach ($info as $key => $value): ?>
					<?php if (in_array($key, array('lastlogin'))): ?>
						<tr><td><?=$key?></td><td><?=date('m-d-Y', $value['sec']);?></td></tr>
						<?php else: ?>
							<tr><td><?=$key?></td><td><?=$value?></td></tr>
						<?php endif ?>
				<?php endforeach ?>
			</table>
		</div>
	</div>
</div>
<div id="clear"></div>
<div class="grid_10">
	<div class="box">
		<h2>
			<a href="#" id="toggle-tables">Order History</a>
		</h2>
		<div class="block" id="tables">
		<?php if (!empty($orders)): ?>
			<table id="orderTable" class="datatable" border="1">
				<thead>
					<tr>
						<?php
						foreach ($headings['order'] as $heading) {
							echo "<th>$heading</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($orders as $order): ?>
						<tr>
							<td><?=date('Y-m-d', $order->date_created->sec);?></td>
							<td>
								<?=$this->html->link($order->order_id, array(
								'Orders::view',
								'args'=>$order->_id),
								array('target' => '_blank'));
								?>
							</td>
							<td>$<?=number_format($order->total, 2);?></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>
		</div>
	</div>
</div>
<div id="clear"></div>
<div class="grid_6">
	<div class="box">
		<h2>
			<a href="#" id="toggle-form">Apply Credits</a>
		</h2>
		<?php if (!empty($admin['superadmin']) && $admin['superadmin'] == true): ?>
			<div class="block" id="forms">
				<?=$this->form->create(null, array('url' => 'Credits::add')); ?>
				<p>
					<?=$this->form->label('Reason For Credit: '); ?>
					<?=$this->form->select('reason', $reasons); ?>
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
					<?=$this->form->hidden('user_id', array('value' => $user->_id)); ?>
					<?=$this->form->submit('Apply'); ?>
				<?=$this->form->end(); ?>
		<?php else: ?>
				<p>Only Super Admins can apply credits</p>
		<?php endif ?>
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
		<p>Total Credit - $<?=number_format($user->total_credit, 2);?></p>
		<?php if (!empty($credits)): ?>
			<table id="creditTable" class="datatable" border="1">
				<thead>
					<tr>
						<?php
						foreach ($headings['credit'] as $heading) {
							echo "<th>$heading</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($credits as $credit): ?>
						<tr>
							<td>
								<?php if (!empty($credit->date_created->sec)): ?>
									<?=date('Y-m-d', $credit->date_created->sec);?>
								<?php else: ?>
									<?=date('Y-m-d', $credit->created->sec);?>
								<?php endif ?>
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
		$('#orderTable').dataTable();
		$('#creditTable').dataTable();
	} );
</script>
<script type="text/javascript">
jQuery(function($){
	$.mask.definitions['~']='[+-]';
	$("#credit_amount").mask("~9.99 ~9.99 999");
});
</script>
