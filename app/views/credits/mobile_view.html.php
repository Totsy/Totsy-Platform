<?php if (!empty($credits)) { ?>
	<h2>Total Credits: <span style="padding:10px 10px 10px 5px; color:#009900;">$<?=$credit?></span></h2>
	<hr />	
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
		<h2>My Credits: <span style="padding:10px 10px 10px 5px; color:#009900;">$0</span>
		</h2>
		<hr />
		<div class="holiday_message">
		<center><strong>You can earn credits by <br />
		<a href="#" onclick="window.location.href='/users/invite';return false;">inviting your friends and family</a></strong></center>
		</div>
	
		<?php } ?>
<p></p><p></p>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
