<?php use admin\models\Email; ?>
<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<div class="grid_16">
	<h2 id="page-heading">E-Mail Management - <?=$event->name?></h2>
</div>
<div class="grid_16">

<?=$this->form->create(); ?>
		<p>
			<?=$this->form->label('Select Email Type'); ?>
			<?=$this->form->select('template', Email::$templates);?>
		</p>
		<p>
			<?=$this->form->label('Special Note to Customer'); ?>
			<?=$this->form->textarea('note');?>
		</p>
		<?=$this->form->submit('Send'); ?>
<?=$this->form->end(); ?>
</div>



<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple",
	width : "600"
});

</script>