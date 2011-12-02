<?php if ($message){ echo $message; } ?>
<div id="fullscreen">

	<div id="login-box">

		<div id="login-box-border" class="login-modal">
			<div id="login-box-container">

				<div class="tt">
					<div></div>
				</div>
				<div class="free_shipping_banner_login"><img src="/img/freeShip-badge.png" /></div>

				<div class="tm">
					<div class="ti">

						<div class="tc login-inner">

							<h1 id="logo">Totsy</h1>

							<div id="intro-copy">

								<p class="red">You have places to be, things to do, and little ones in tow.</p>

								<p>At Totsy, moms on the go and moms to be experience the <br />shopping they desire at prices they can't resist.</p>

							</div>

							<div class="sign-in-container">
								<div id="sign-in-box" class="r-container">
									<div class="tl"></div>
									<div class="tr"></div>
									
									<div class="r-box lt-gradient-1">
										<h2>Member Sign In</h2>

										<?=$this->form->create(null,array('id'=>'loginForm'));?>
										<?=$this->form->field('email', array('class'=>"validate['required']  inputbox", 'id'=>"email"));?>
										<?=$this->form->field('password', array(
												'class'=>"validate['required'] inputbox",
												//'name' => 'password',
												'id' => 'password',
												'type' => 'password'));?>
										<?=$this->form->checkbox('remember_me', array('class' => 'fl checkbox')); ?> Remember Me <br/>
										<?=$this->form->submit('Sign In', array('class'=>"button fr"));?> 
										<?=$this->form->end();?>
										<div style="clear:both;"></div>
										<p style='margin-top: 10px'> <?=$this->html->link('Forgot your password?','/reset', array('class'=>"md", 'title'=>"Forgot your password?"))?> </p>
										<hr />
										<a href="#" onclick="fblogin();return false;"><img src="/img/fb_login_btn.png"></a>
									</div>
									<div class="bl"></div>
									<div class="br"></div>
								</div>
							</div>

							<div class="register-container">
								<div id="register-box" class="r-container">
									<div class="tl"></div>
									<div class="tr"></div>
									<div class="r-box lt-gradient-1">
										<h2>Become a Member</h2>

										<p>Become a member today for access to brand-specific sales, up to 90% off retail, just for you and the kids, ages 0-8. Prenatal care products, baby gear, travel accessories, bedding and bath, children's clothing, toys, and educational materials &mdash; and that's just the start.</p>
										<?=$this->html->link('Request Membership','/register', array('class' => 'button')); ?>
										
									</div>
									<div class="bl"><!-- --></div>
									<div class="br"><!-- --></div>

								</div>
							</div>

							<p class="login-sig clear">Exclusive access, Top brands. Great deals. <span class="red">The savvy mom shops at Totsy.com</span></p>
							<p style="text-align:center; font-size:11px; color:#333;">* Offer expires 30 days after registration</p>

						</div>

					</div>
				</div>

				<div class="tb">
					<div></div>
				</div>

			</div>

		</div>

	</div>

</div>

<div id="footer">
	<?php echo $this->view()->render(array('element' => 'footerNavPublic')); ?>
</div>
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
<script>
	//your fb login function
	function fblogin() {
	FB.login(function(response) {
		}, {perms:'publish_stream,email,user_about_me,user_activities,user_birthday,user_groups,user_interests,user_location'});
	}
</script>
