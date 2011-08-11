<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Select Event for <?=$type?> Administration</h2>
</div>
<div class='clear'></div>
<div>
	<?=$this->form->create(null, array('id' => 'monthform')); ?>
	<?=$this->form->label("month_delay", "Events created from Last ", array('style' => 'font-weight:bold; font-size:13px;')); ?>
	<?=$this->form->select('month_delay',array('0' => 'select', '6' => '0-6' ,'48' => 'all'), array('onchange' => "filter()", 'id' => 'month_delay', 'style' => 'width:120px;')); ?>
	<?=$this->form->label("month_delay", " Months", array('style' => 'font-weight:bold; font-size:13px;')); ?>
	<?=$this->form->end(); ?>
</div>
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
	function filter() {
		$('#monthform').submit();
	};
</script>