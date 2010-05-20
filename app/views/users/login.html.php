<?=$this->form->create();?>
<?=$this->form->field('email', array('class'=>'required'));?>
<?=$this->form->field('password', array('type'=>'password','class'=>'required'));?>
<?=$this->form->submit('Login');?>
<?=$this->form->end();?>
<?=$this->html->link('Request Membership','/register')?>
