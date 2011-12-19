<?php echo $this->html->script('mootools-1.2.4-core-nc.js');?>
<?php echo $this->html->script('mootools-1.2.4.4-more.js');?>
<?php echo $this->html->script('formcheck.js');?>
<?php echo $this->html->script('en.js');?>


<script type="text/javascript">
    window.addEvent('domready', function(){
        new FormCheck('registerForm');
    });
</script>

<h1 id="logo">Totsy</h1>

<div id="intro-copy">

	<h2 style="margin-top:30px"><span>Become a</span> MEMBER</h2>

</div>

<div class="message">
	<?php if($message){echo "$message"; } ?>
</div>

<div class="r-container clear reg-list">
	
	<div class="tl"></div>
	<div class="tr"></div>
	<div class="r-box lt-gradient-1">
		
		<strong class="red">Why you will love Totsy</strong>
		
		<ul class="bugs columns-2">
			<li>Exclusive sales for moms, children &amp; babies.</li>
			<li>Sales last up to 3 days, plenty of time to shop.</li>
			<li>Savings of up to 70% off retail.</li>
			<li>For every purchase, one tree is planted.</li>
			<li>Membership is free</li>
			<li>We are 100% green.</li>
		</ul>
		
	</div>
	<div class="bl"></div>
	<div class="br"></div>

</div>

<?php echo $this->form->create(null,array('id'=>'registerForm')); ?>
<div class="form-row">
<?php echo $this->form->label('fname','First Name',array('class'=>'label'));?>&nbsp;
<?php echo $this->form->text('firstname', array('class'=>"validate['required'] inputbox",'id'=>"fname"));?>
<?php echo $this->form->label('lname','Last Name',array('class'=>'label'));?>&nbsp;
<?php echo $this->form->text('lastname', array('class'=>"validate['required'] inputbox",'id'=>"lname"));?>
</div>

<div class="form-row">
<?php echo $this->form->label('email','Email',array('class'=>'label'));?>&nbsp;
<?php echo $this->form->text('email', array('class'=>"validate['required','email'] inputbox",'id'=>"email"));?>
<?php echo $this->form->label('confirmemail','Confirm email',array('class'=>'label'));?>&nbsp;
<?php echo $this->form->text('confirmemail', array('class'=>"validate['required','email'] inputbox",'id'=>"confirmemail"));?>
</div>

<div class="form-row">
<?php echo $this->form->label('username','Username',array('class'=>'label'));?>&nbsp;
<?php echo $this->form->text('username', array('class'=>"validate['required'] inputbox",'id'=>"username"));?>
<?php echo $this->form->label('password','Password',array('class'=>'label'));?>
<?php echo $this->form->password('password', array(
		'class'=>"validate['required'] inputbox", 
		'name' => 'password', 
		'id' => 'password'));?>
</div>

<div class="submit-row">
<?php echo $this->form->checkbox('terms', array('class'=>"validate['required']", 'style'=>"float:left;margin-right:4px"));?>
<span class="sm reg-tos">By requesting membership, I accept the <?php echo $this->html->link('Terms and Conditions','#')?> of Totsy, and accept to receive sale email newsletters. Totsy will never sell or give my email to any outside party.</span>

<button type="submit" name="submit" id="submit-btn" class="flex-btn-2"><?php echo 'Register';?></button>				
</div>

<?php echo $this->form->end(); ?>