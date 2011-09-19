<div class="box">
	<h2>Item Image Status</h2>
	<table>
		<thead>
			<tr>
				<th>Vendor Style ID</th>
				<th>Primary</th>
				<th>Zoom</th>
				<th>Alternates</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($event->items as $item): ?>
			<tr>
				<?php $images = $item->images(); ?>
				<td><?=$this->html->link($item->vendor_style, array(
					'controller' => 'items', 'action' => 'view', 'id' => $item->_id
				)); ?></td>
				<td class="<?= $images['primary'] ? 'positive' : 'negative'; ?>">
					<?=$images['primary'] ? '✔' : '✘'; ?>
				</td>
				<td class="<?= $images['zoom'] ? 'positive' : 'negative'; ?>">
					<?=$images['zoom'] ? '✔' : '✘'; ?>
				</td>
				<td>
					<?=$images['alternate'] ? count($images['alternate']) : '–'; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
