<?php $this->title("My Credits"); ?>
<h1 class="p-header">My Credits</h1>
<?=$this->menu->render('left'); ?>

<div id="name">
	Total Credit is:
	<?php if (!empty($credit)): ?>
		$<?=$credit?>
	<?php endif ?>
</div>
<table border="0" cellspacing="5" cellpadding="5">
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