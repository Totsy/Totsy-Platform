<?php $this->title("My Credits"); ?>

<div class="grid_16">
	<h2 class="page-title gray">My Credits</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>

<div class="grid_11 omega roundy grey_inside b_side">

	<h2 class="page-title gray">My Credits</h2>
	<hr />
	<?php if (!empty($credit)) { ?>
		<div id="name" style="padding:10px 10px 10px 5px; color:#009900;" class="order-table">
			<strong class="fl">Total Credits: $<?=$credit?></strong>
			<div style="clear:both;"></div>
		</div>
		<table border="0" cellspacing="5" cellpadding="5" width="100%" class="order-table" style="margin-top:10px;">
			<tr>
				<th>Date</th>
				<th>Amount</th>
				
			</tr>
			<?php foreach ($credits as $credit): ?>
			<tr>
			<td><?=date('Y-m-d', $credit->_id->getTimestamp())?></td>
			<td>
			<?php if (!empty($credit->credit_amount)) { ?>
					<?php echo "$".number_format(abs($credit->credit_amount),2); ?>
				<?php  } else {  ?>
					<?php echo "$".number_format(abs($credit->amount),2); ?>
			<?php } ?>
			</td>
			
			<tr>
			<?php endforeach ?>
		</table>
		<?php } else { ?>
		<center><strong>Earn credits by <a href="/users/invite" title="inviting your friends and family">inviting your friends and family.</a></strong></center>
			<br />
		</div>
		<?php } ?>
	<br />

</div>
</div>
<div class="clear"></div>
