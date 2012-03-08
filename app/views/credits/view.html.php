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
	<?php if (!empty($userInfo['total_credit'])) { ?>
		<div id="name" style="padding:10px 10px 10px 5px; color:#009900;" class="order-table">
			<strong class="fl">Total Credits: $<?php echo number_format($userInfo['total_credit'],2,',','.');?></strong>
			<div style="clear:both;"></div>
		</div>
		<table border="0" cellspacing="5" cellpadding="5" width="100%" class="order-table credits" style="margin-top:10px;">
			<thead>
			<tr>
				<th>Date</th>
				<th>Amount</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($credits as $thecredit): ?>
				<tr>
				<td><?= date('Y-m-d', $thecredit->_id->getTimestamp());?></td>
				<td>
				<?php if (!empty($thecredit->credit_amount)) { 
				
						$creditAmount = $thecredit->credit_amount;
						
						// positive amount
						if ($creditAmount > 0) {
							echo "<span class='credit-pos'>+ $". number_format($creditAmount,2) . "</span>";
						}
						// is it negative amount?
						elseif ($creditAmount < 0 ) {
							$creditAmount = abs($creditAmount);
							echo "<span class='credit-neg'>&minus; $" . number_format($creditAmount,2,',','.') . "</span>";
						}
					}
				?>
				</td>
			<?php endforeach ?>
			</tbody>
		</table>
		<div class="credit-total">Total Credits: $<?php echo number_format($userInfo['total_credit'],2,'.',' ')?></div>
		<?php } else { ?>
		<center><strong>Earn credits by <a href="/users/invite" title="inviting your friends and family">inviting your friends and family.</a></strong></center>
			<br />
		</div>
		<?php } ?>
	<br />

</div>
</div>
<div class="clear"></div>
