<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Select Event for <?=$type?> Administration
	<?php if ($environment == 'local'): ?>
		 - Dev Environment - Only Last 3 Months Events
	<?php endif ?>
	</h2>
</div>
<div class='clear'></div>
<div class="grid_16">
	<?=$this->events->build($events, array('type' => $type))?>
</div>
<div class='clear'></div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>