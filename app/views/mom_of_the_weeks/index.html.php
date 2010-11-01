<p>If you haven't submitted data, here's a handy dandy form for you.</p>

<h2>This is the contest test form.</h2>

<?=$this->form->create(); ?>
	<?=$this->form->field('answer_1');?>
	<?=$this->form->field('answer_2');?>
	<?=$this->form->field('answer_3');?>
	<?=$this->form->field('answer_4');?>
	<?=$this->form->field('answer_5');?>
	<?=$this->form->field('firstname');?>
	<?=$this->form->field('lastname');?>
	<?=$this->form->field('email');?>
	<?=$this->form->field('confirmemail');?>
	<?=$this->form->field('password');?>
	Accept terms: <?=$this->form->checkbox('terms');?><br />
	<?=$this->form->submit('Add Entry'); ?>
<?=$this->form->end(); ?>