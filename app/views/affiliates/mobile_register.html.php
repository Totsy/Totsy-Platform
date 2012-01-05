<script type="text/javascript">

	var affiliateName = "";
	var categoryName = "";
	
	<?php if($affiliateName) {  ?>
		affiliateName= "<?php echo $affiliateName?>";	
	<?php } ?>
	
	<?php if($affiliateName) { ?>
		categoryName= "<?php echo $categoryName?>";
	<?php } ?>
	
	<?php if($affBgroundImage) { ?>
		affBgroundImage = "<?php echo $affBgroundImage?>"; 	
	<?php } ?>
	
</script>
<?php if ($message){ echo $message; } ?>
<h2>Sign Up <span style="color:#999; font-size:12px; float:right; margin:5px 0px 0px 0px;">Already a Totsy member? <a href="#" onclick="window.location.href='/login';return false;">Sign In</a></span></h2>
<hr />
<?php if ($message){ ?>
<?php echo $message; ?>
<?php } ?>
<form id="registerForm" method="post">

									<div>
										<?php echo $this->form->label('email', 'Email<span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?php echo $this->form->email('email', array('class' => 'inputbox')); ?>
										<?php echo $this->form->error('email'); ?>
									</div>
									<div>
										<?php echo $this->form->label('confirmemail', 'Confirm Email<span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?php echo $this->form->email('confirmemail', array('class' => 'inputbox')); ?>
										<?php echo $this->form->error('confirmemail'); ?>
										<?php echo $this->form->error('emailcheck'); ?>
									</div>

									<div>
									<?php echo $this->form->label('password','Password<span>*</span>', array(
										'class'=>'required',
										'escape' => false
										));
									?>
									<?php echo $this->form->password('password', array(
											'class'=>"inputbox",
											'name' => 'password',
											'id' => 'password'));
									?>
									<?php echo $this->form->error('password'); ?>
									</div>
									<div>
									<?php echo $this->form->checkbox('terms', array("checked" => "checked", 'style'=>"float:left;margin-right:4px; display: none;"));?>
									</div>
									<div style="font-size:10px; margin:0px; text-align:center">
											By clicking register you accept our 
											<?php echo $this->html->link('Terms and Conditions','pages/terms')?>
									</div>
									<br>
									<?php echo $this->form->submit('Sign Up'); ?>
									<?php echo $this->form->error('terms'); ?>
									</div>
								<?php echo $this->form->end(); ?>
				