<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Select Event for <?=$type?> Administration</h2>
</div>
<div class='clear'></div>
<?php if(!empty($events)) :?>
<div class="grid_16">
	<?=$this->events->build($events, array('type' => $type))?>
</div>
<?php endif ?>
<div class='clear'></div>


<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>