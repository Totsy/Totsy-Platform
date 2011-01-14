<?php $this->title("My Credits"); ?>
<h1 class="p-header">My Credits</h1>
<?=$this->menu->render('left'); ?>

<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<h2 class="gray mar-b">My Credits</h2>
		<div id="name">
			<b>Credit Total:
			<?php if (!empty($credit)): ?>
				$<?=$credit?>
			<?php endif ?>
			</b>
		</div>
		<table border="0" cellspacing="5" cellpadding="5" width="100%" class="order-table">
			<tr>
				<th>Date</th>
				<th>Amount</th>
				<th>Reason</th>
			</tr>
			<?php foreach ($credits as $credit): ?>
				<?php if ($credit->created): ?>
					<tr>
						<td><?=date('Y-m-d', $credit->created->sec)?></td>
						<td>$<?=$credit->credit_amount?></td>
						<td><?=$credit->reason?></td>
					</tr>
				<?php endif ?>
			<?php endforeach ?>
	
		</table>
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
