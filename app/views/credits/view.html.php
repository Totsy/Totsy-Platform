<?php $this->title("My Credits"); ?>
<h1 class="p-header">My Credits</h1>
<?=$this->menu->render('left'); ?>

<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<h2 class="gray mar-b">My Credits</h2>
		<hr/>
		
		<?php if (!empty($credit)) { ?>
		<div id="name" style="padding:10px 10px 10px 5px; color:#009900;" class="order-table">
			<strong class="fl">Total Credits: $<?=$credit?></strong>
			<div style="clear:both;"></div>
		</div>
		<table border="0" cellspacing="5" cellpadding="5" width="100%" class="order-table">
			<tr>
				<th>Date</th>
				<th>Amount</th>
				<th>Description</th>
			</tr>
			<?php foreach ($credits as $credit): ?>
			<tr>
			<td><?=date('Y-m-d', $credit->_id->getTimestamp())?></td>
			<td>$<?=$credit->amount?></td>
			<td><?=$credit->description?></td>
			<tr>
			<?php endforeach ?>
		</table>
		<?php } else { ?>
		<div id="name" style="padding:10px 10px 10px 5px;" class="order-table">
			<strong class="fl">Earn credits by <a href="/users/invite" title="inviting your friends and family">inviting your friends and family.</a></strong>
			<div style="clear:both;"></div>
		</div>
		<?php } ?>
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
