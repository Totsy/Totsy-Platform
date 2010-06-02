<?=$this->form->create(); ?>
    <?=$this->form->field('title');?>
    <?=$this->form->field('body', array('type' => 'textarea'));?>
    <?=$this->form->submit('Add Page'); ?>
<?=$this->form->end(); ?>