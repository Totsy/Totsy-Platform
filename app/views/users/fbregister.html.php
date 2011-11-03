<div id="fullscreen">
	<div id="login-box">
		<div id="login-box-border" class="register-modal">
			<div id="login-box-container">
				<div class="tt">
					<div><!-- --></div>
				</div>
				<div class="free_shipping_banner_reg"><img src="/img/freeShip-badge.png" /></div>

				<div class="tm">

					<div class="ti">

						<div class="tc login-inner register-inner">

							<div id='logo'>
                            <h1>
                              <a href="/login/?fbcancel=true" title="Totsy.com">
                                Totsy</a>
                              </h1>
                            </div>
							
							<div id="intro-copy">
								<h2 style="margin-top:20px"><span>Become a</span> MEMBER
								<br />
								<?=$this->html->link('Already a Member?', '/login/?fbcancel=true', array('style' => 'font-size:12px;'));?></h2>
							</div>

							<div class="message">
								<?php if($message){echo "$message"; } ?>
							</div>


							<div id="" class="r-container clear">
								<div class="tl"></div>
								<div class="tr"></div>
								<div class="r-box lt-gradient-1">
                                <h2> <img src="https://graph.facebook.com/<?=$fbuser['id']?>/picture"> Hi <?=$fbuser['name']?> - you're one step away from joining with Facebook</h2>
                                <hr />
<?    print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/fb_register\']);">'; ?>

               <!-- Commnented Firstname, Lastname and Zip code --->

									<!-- div class="form-row">
										<?=$this->form->label('firstname', 'First Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>


										<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
										<?=$this->form->error('firstname'); ?>
									</div>


									<div class="form-row">
										<?=$this->form->label('lastname', 'Last Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
										<?=$this->form->error('lastname'); ?>
									</div>
							<div class="form-row">
										<?=$this->form->label('zip', 'Zip Code <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
										<?=$this->form->error('zip'); ?>
									</div -->
									<!-- ************************************************************** -->
									<div class="form-row">
										<?=$this->form->label('email', 'Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('email', array('class' => 'inputbox')); ?>
										<?=$this->form->error('email'); ?>
									</div>
									<div class="form-row">
										<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('confirmemail', array('class' => 'inputbox')); ?>
										<?=$this->form->error('confirmemail'); ?>
										<?=$this->form->error('emailcheck'); ?>
									</div>
									<div class="form-row">
									<?=$this->form->label('password','Password <span>*</span>', array(
										'class'=>'required',
										'escape' => false
										));
									?>
									<?=$this->form->password('password', array(
											'class'=>"inputbox",
											'name' => 'password',
											'id' => 'password'
										));
									?>
									<?=$this->form->error('password'); ?>
									</div>
									<div class="form-row_">
									<?=$this->form->checkbox('terms', array("checked" => "checked", 'style'=>"float:left;margin-right:4px; display: none;"));?>
									</div>
									<span class="sm reg-tos" style="overflow:visible!important;">
											By clicking register you accept our 
											<?=$this->html->link('Terms and Conditions','pages/terms')?>.
									</span>
										<?=$this->form->submit('Register', array('class' => 'button')); ?>
										<?=$this->form->error('terms'); ?>
									</div>
								<?=$this->form->end(); ?>
								</div>
								<div class="r-container clear reg-list">
								<div class="tl"></div>
								<div class="tr"></div>
								<div class="r-box lt-gradient-1">
									<strong class="red">Why you will love Totsy</strong>
									<ul class="bugs columns-2">
										<li>Exclusive sales for moms, children &amp; babies.</li>
										<li>Sales last up to 3 days, plenty of time to shop.</li>
										<li>Savings of up to 90% off retail.</li>
										<li>For every purchase, one tree is planted.</li>
										<li>Membership is free</li>
										<li>We are 100% green.</li>
									</ul>
								</div>
								<div class="bl"></div>
								<div class="br"></div>
							</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tb">
					<div><!-- --></div>
				</div>

			</div>
		</div>
	</div>
</div>

<div id="footer">
	<?php echo $this->view()->render(array('element' => 'footerNavPublic')); ?>
</div>

<script>
	document.getElementById("password").focus();
</script>

<!-- Google Code for Homepage Remarketing List -->
<script type="text/javascript">
/* <![CDATA[ */
	var google_conversion_id = 1019183989;
	var google_conversion_language = "en";
	var google_conversion_format = "3";
	var google_conversion_color = "666666";
	var google_conversion_label = "8xkfCIH8iwIQ9Yb-5QM";
	var google_conversion_value = 0;
/* ]]> */
</script>

<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>

<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1019183989/?label=8xkfCIH8iwIQ9Yb-5QM&amp;guid=ON&amp;script=0"/>
	</div>
</noscript>
<!-- END OF Google Code for Homepage Remarketing List -->
