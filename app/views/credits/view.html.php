<?php $this->title("My Credits"); ?>
<h1 class="p-header">My Credits</h1>
<?=$this->menu->render('left'); ?>

<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<h2 class="gray mar-b">My Credits</h2>
		<hr/>
		<div id="name" style="padding:10px 10px 10px 5px; background:#f1f1f1; color:#009900;" class="order-table">
			<strong class="fl">Total Credits</strong>
			<strong class="fr">
			<?php if (!empty($credit)): ?>
				$<?=$credit?>
			<?php endif ?>sss
			</strong>
			<div style="clear:both;"></div>
		</div>
		<table border="0" cellspacing="5" cellpadding="5" width="100%" class="order-table">
			<tr>
				<th>Date</th>
				<th>Credit Amount</th>
				<th>Reason</th>
			</tr>
			<?php foreach ($credits as $credit): ?>
				<?php if ($credit->created): ?>
					<tr>
						<td><?=date('Y-m-d', $credit->created->sec)?></td>
						<td class="price">$<?=$credit->credit_amount?></td>
						<td><?=$credit->reason?></td>
					</tr>
				<?php endif ?>
			<?php endforeach ?>
	
		</table>
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
