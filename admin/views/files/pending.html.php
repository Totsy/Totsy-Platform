<?php if ($files): ?>
	<ul>
	<?php foreach($files as $item): ?>
		<li class="file">
			<?=$this->view()->render(
				array('element' => 'file'),
				compact('item') + array('editable' => true)
			); ?>
			<div class="actions">
				<?=$this->html->link('delete', array('action' => 'delete', 'id' => $item->_id)); ?>
				<?=$this->html->link('auto-associate', array('action' => 'associate', 'id' => $item->_id)); ?>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<div class="none-available">No pending files available.</div>
<?php endif; ?>