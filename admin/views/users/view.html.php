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
<div class="grid_8">
    <?php if ($deactivated): ?>
        <h4 style="float:left;">Account Status: <span style="color:#FF0000"> Inactive </span></h4>
        &nbsp
        <input type="button" value="Activate Account" onclick="accountStatus('<?php echo $user->_id?>', 'activate')"/>

    <?php else: ?>
        <h4 style="float:left;">Account Status: <span style="color:#008000">Active </span></h4>
        &nbsp
        <input type="button"  id="initiate" class="button" value="Deactivate Account"/>
    <?php endif; ?>
    <a href="#" id="history">view history</a>
</div>
<div class="clear"></div>
<div id="comment" class="grid_8">
    <select name="deactivate_reason" id="deactivate_reason">
        <option value="">Select User's Reason</option>
        <option value="user_request">Requested By User</option>
        <option value="malicious_user">Malicious User</option>
        <option value="other">Other</option>
    </select>
    <label id="other_label"> Other Reason: </label>
    <input id="other_reason" name="other_reason" type="text"/>
    <label> Additional Comments: </label>
    <textarea name="additional_comments" id="additional_comments"cols="50" rows="5">
    </textarea>
    <input type="button"  id="deactivate" class="button" value="Deactivate" onclick="accountStatus('<?php echo $user->_id?>', 'deactivate')"/>
    <input type="button"  id="cancel" class="button" value="cancel"/>
</div>
<div class="clear"></div>
<div id="modal"></div>
<br/>
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
<div id="clear"></div>
<div class="grid_16">
	<div class="box">
		<h2>
			<a href="#" id="toggle-tables">Promocodes Used</a>
		</h2>
		<div class="block" id="tables">
		<?php if (!empty($promocodes_used)): ?>
			<table id="promoTable" class="datatable" border="1">
				<thead>
					<tr>
						<?php
						foreach ($headings['promo'] as $heading) {
							echo "<th>$heading</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($promocodes_used as $promocode): ?>
						<tr>
							<td>
								<?php if (!empty($promocode['date_created']->sec)): ?>
									<?=date('Y-m-d', $promocode['date_created']->sec);?>
								<?php endif?>
							</td>
							<td>
								<?php if ($promocode['order_id']): ?>
									<?=$this->html->link($promocode['order_id'], array(
								'Orders::view',
								'args'=>$promocode['order_id']),
								array('target' => '_blank'));
								?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($promocode['code']): ?>
									<?=$this->html->link($promocode['code'], array(
										'Promocodes::edit',
										'args'=>$promocode['code_id']),
										array('target' => '_blank'));?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($promocode['type']): ?>
									<?=$promocode['type'];?>
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
    $("#comment").hide();
     $("#other_reason").hide();
     $("#other_label").hide();
     $("#status_history").hide();
	$(document).ready(function() {
		$('#orderTable').dataTable();
		$('#creditTable').dataTable();
		$('#historyTable').dataTable();
	} );
</script>
<script type="text/javascript">
jQuery(function($){
	$.mask.definitions['~']='[+-]';
	$("#credit_amount").mask("~9.99 ~9.99 999");
});
$("#deactivate_reason").change(function(){
    if ($("#deactivate_reason option:selected").val() == "other") {
        $("#other_label").show();
        $("#other_reason").show();
    } else {
        $("#other_label").hide();
         $("#other_reason").hide();
    }
});
</script>
<script type="text/javascript">
 $("#history").click(function(){
    if ($("#status_history").is(":hidden")) {
        $("#status_history").show();
    } else {
        $("#status_history").hide();
    }
 });
  $("#cancel").click(function(){
        $("#comment").slideUp();
        $("#initiate").show();
 });
  $("#initiate").click(function () {
    $("#initiate").hide();
    if ($("#comment").is(":hidden")) {
        $("#comment").show("slow");
    }
});
 $('#history').click(function(){
        $('#modal').load('/users/deactivateHistory/<?php echo $user->_id?>').dialog({
            autoOpen: false,
            modal:true,
            width: 1000,
            height: 450,
            position: 'top',
            close: function(ev, ui) {}
        });
        $('#modal').dialog('open');
    });
function accountStatus(id, type) {
    var reason = $("#deactivate_reason option:selected").val();
    if (reason == "other") {
        var reason = $("#other_reason").val();
    }
    var comment = $("#additional_comments").val();
    if ((reason != "" && type == 'deactivate') || type == 'activate' ) {
        $.post("/users/accountStatus/" + id,{type:type,deactivate_reason:reason,comment:comment}, function(){
            location.reload();
        });
    } else {
        alert("Please Select a reason...");
    }

}

</script>
