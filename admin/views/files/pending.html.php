<?php if ($files): ?>
	<ul>
	<?php foreach($files as $file): ?>
		<li>
			<?=$file->name ?: $file->_id; ?>
			<?php
				$meta = array();

				if ($file->created_date) {
					$meta[] = date('m/d/y', $file->created_date->sec);
				}
				$meta[] = sprintf('%.2f MB', $file->file->getSize() / MEGABYTE);

				if ($file->mime_type) {
					$meta[] = $file->mime_type;
				}
			?>
			(<?=implode(', ', $meta) ?>)

			<div class="actions">
			<?=$this->html->link('delete', array('action' => 'delete', 'id' => $file->_id)); ?>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<div class="none-available">No pending files available.</div>
<?php endif; ?>