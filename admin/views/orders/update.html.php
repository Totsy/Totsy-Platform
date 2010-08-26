<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>
<?=$this->html->style('admin');?>



<h1>Order Administration</h1>
<br>
<?=$this->form->create(null, array('enctype' => "multipart/form-data")); ?>
	<?=$this->form->label('Upload Order File: '); ?>
	<?=$this->form->file('upload'); ?>
	<?=$this->form->submit('Submit'); ?>
<?=$this->form->end(); ?>
<?php if (!empty($updated)): ?>
	<?php var_dump($updated) ?>
<?php endif ?>

