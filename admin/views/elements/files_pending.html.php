<?php

extract(array(
	'item' => null
), EXTR_SKIP);

$refreshUrl = array('controller' => 'files', 'action' => 'pending');
$associateUrl = array('controller' => 'files', 'action' => 'associate', 'scope' => 'pending');

if ($item) {
	$refreshUrl['on'] = $associateUrl['on'] = (string) $item->_id;
}

?>
<div id="pending" class="box">
	<h2>Manage Pending Files</h2>
	<div class="actions">
		<?=$this->html->link('refresh', $refreshUrl, array(
			'class' => 'refresh', 'target' => '#pending-data'
		)); ?>
		<?=$this->html->link('auto-associate all', $associateUrl); ?>
	</div>
	<div class="block">
		<p>
			Files not yet associated with any item or event.
			These files have not been resized and are umodified from their <em>original</em> state.
		</p>
		<div id="pending-data"><!-- Populated through an AJAX request. --></div>
	</div>
</div>