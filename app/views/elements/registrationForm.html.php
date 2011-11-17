	<h2 style="margin-bottom:20px;">Join with Facebook</h2>
	<a href="javascript:;" onclick="fblogin();return false;"><img src="/img/sign_in_fb.png" class="fr"></a>
	<br />
	
<h2 style="margin-top:30px;margin-bottom:20px;">Or join with email</h2>	

<?php 
if (preg_match('/join/',$_SERVER['REQUEST_URI'])) { 
?>
	<form id="registerForm" method="post" onsubmit="_gaq.push(['_trackPageview', '/vpv/join']);">
<?php
}
elseif (preg_match('/register/',$_SERVER['REQUEST_URI'])) { 
?>
	<form id="registerForm" method="post" onsubmit="_gaq.push(['_trackPageview', '/vpv/register']);"> 
<?php
}
elseif (preg_match('/a/',$_SERVER['REQUEST_URI'])) { 
?>
	<form id="registerForm" method="post" onsubmit="_gaq.push(['_trackPageview', '/vpv/affiliate']);">
<?php
}
else { 
?>
	<form id="registerForm" method="post" onsubmit="_gaq.push(['_trackPageview', '/vpv/register']);"> 
<? } ?>

	<div style="width:70px; float:left">

	<?=$this->form->label('email', 'Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	</div>

	<div style="float:right">
	<?=$this->form->text('email', array('class' => 'inputbox', 'style' => 'width:190px')); ?>
	</div>
	<?=$this->form->error('email'); ?>
	
	<div style="width:70px; float:left">
	
	<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>
	</div>

	<div style="float:right">

	<?=$this->form->text('confirmemail', array('class' => 'inputbox', 'style' => 'width:190px')); ?>
	</div>
	
	<?=$this->form->error('confirmemail'); ?>
	
	<?=$this->form->error('emailcheck'); ?>

	<div style="width:70px; float:left">
	<?=$this->form->label('password','Password <span>*</span>', array(
		'class'=>'required',
		'escape' => false
		));
	?>
	</div>

	<div style="float:right">


	<?=$this->form->password('password', array(
		'class'=>"inputbox",
		'name' => 'password',
		'id' => 'password', 'style' => 'width:190px'
		));
	?>
	</div>
	<?=$this->form->error('password'); ?>
	<?=$this->form->checkbox('terms', array(
		"checked" => "checked", 
		'style'=>"float:left;margin-right:4px; display: none;"
		));
	?>
	
	<div class="clear"></div>
	<div style="font-size:11px;margin-top:10px; color:#999999;">
		By joining you accept our 
		<?=$this->html->link('terms and conditions','pages/terms')?>.
	</div>	
	
	<input class="button fl" type="button" value="Join Now" onclick="return setIframe();">
	
	<?//=$this->form->submit('Join Now', array('class' => 'button fr','style' => 'width:100px; height 28px; font-weight:important;'));?>
	
	<?=$this->form->error('terms'); ?>
	
	
<?=$this->form->end(); ?>

<div id="psm" style="display:none;"></div>
<div id="fbshoes" style="display:none;"></div>


<script type="text/javascript"> 
	
  function setIframe() {
	var tiframe = document.getElementById('psm').innerHTML = '<iframe src="/static/signup-tracking.html" style="border:none;width:1px;height:1px;" marginheight="0" marginwidth="0" frameborder="0"></iframe>';
<?php
	if (preg_match('/facebookshoes/',$_SERVER['REQUEST_URI'])) {
?>
  	var fbshoesiframe = document.getElementById('fbshoes').innerHTML = '<iframe src="/static/facebookshoes-tracking.html" style="border:none;width:1px;height:1px;" marginheight="0" marginwidth="0" frameborder="0"></iframe>';
<?php
	}
?>
	setTimeout ( "pauseFunction()", 1500 );
  } 

function pauseFunction ( )
{
  document.forms["registerForm"].submit();
}
  
</script>
