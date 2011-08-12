<?php if ($files): ?>
	<ul>
	<?php foreach($files as $item): ?>
		<li class="file">
			<?=$this->view()->render(array('element' => 'file'), compact('item')); ?>
			<div class="actions">
				<?=$this->html->link('delete', array('action' => 'delete', 'id' => $item->_id)); ?>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<div class="none-available">No orphaned files available.</div>
<?php endif; ?>