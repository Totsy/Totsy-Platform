	<h2>Join with Facebook</h2>
	<hr />
	<a href="javascript:;" onclick="fblogin();return false;"><img src="/img/sign_in_fb.png"></a>

<h2 style="margin-top:9px;">Or Join with Email</h2>	
<hr />
<?php if (preg_match('/join/',$_SERVER['REQUEST_URI'])) {
	print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/join\']);">'; }
?>
<?php if (preg_match('/register/',$_SERVER['REQUEST_URI'])) {
	print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/register\']);">'; }
?>
<?php if (preg_match('/a/',$_SERVER['REQUEST_URI'])) {
	print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/affiliate\']);">'; }
?>
	<?=$this->form->label('email', 'Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('email', array('class' => 'inputbox', 'style' => 'width:188px')); ?>
	<?=$this->form->error('email'); ?>
	
	<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('confirmemail', array('class' => 'inputbox', 'style' => 'width:188px')); ?>
	<?=$this->form->error('confirmemail'); ?>
	<?=$this->form->error('emailcheck'); ?>

	<?=$this->form->label('password','Password <span>*</span>', array(
		'class'=>'required',
		'escape' => false
		));
	?>

	<?=$this->form->password('password', array(
		'class'=>"inputbox",
		'name' => 'password',
		'id' => 'password', 'style' => 'width:188px'
		));
	?>

	<?=$this->form->error('password'); ?>
	<?=$this->form->checkbox('terms', array(
		"checked" => "checked", 
		'style'=>"float:left;margin-right:4px; display: none;"
		));
	?>
	
	<?=$this->form->submit('Join Now', array(
		'class' => 'button fl'
		));
	?>
	
	<span class="fl" style="margin:5px 0px 0px 10px;">The savvy mom shops at Totsy!</span>
	<div class="clear"></div>
	<div style="font-size:11px; padding:5px; margin-top:10px;">
		By joining you accept our 
		<?=$this->html->link('terms and conditions','pages/terms')?>.
	</div>
	
	<div class="fr" style="font-size:11px; padding:5px; margin:10px 66px 0px 0px;">
		*Offer expires 30 days after registration.
	</div>
	
	<?=$this->form->error('terms'); ?>
	
<?=$this->form->end(); ?>