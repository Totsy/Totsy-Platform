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
                              <a href='/' title="Totsy.com">
                                Totsy</a>
                              </h1>
                            </div>


							<div id="intro-copy">
								<h2 style="margin-top:20px"><span>Become a</span> MEMBER
								<br />
								<?=$this->html->link('Already a Member?', '/', array('style' => 'font-size:12px;'));?></h2>
							</div>

							<div class="message">
								<?php if($message){echo "$message"; } ?>
							</div>

							<div class="r-container clear">
								<div class="tl"></div>
								<div class="tr"></div>
								<div class="r-box lt-gradient-1">
                                 <div style="width:56%; display:block; float:left; margin-right:10px; ">
                                <h3 style="color:#999; font-size:18px;">Register</h3>
								<hr />
<?php 
print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/affiliate\']);">';
?>
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
									<div class="form-row_">
										<?=$this->form->label('email', 'Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('email', array('class' => 'inputbox', 'style' => 'width:188px')); ?>
										<?=$this->form->error('email'); ?>
									</div>
									<div class="form-row_">
										<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('confirmemail', array('class' => 'inputbox', 'style' => 'width:188px')); ?>
										<?=$this->form->error('confirmemail'); ?>
										<?=$this->form->error('emailcheck'); ?>
									</div>

									<div class="form-row_">
									<?=$this->form->label('password','Password <span>*</span>', array(
										'class'=>'required',
										'escape' => false
										));
									?>
									<?=$this->form->password('password', array(
											'class'=>"inputbox",
											'name' => 'password',
											'id' => 'password', 'style' => 'width:188px'));
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
									<br>
									<?=$this->form->submit('Register', array('class' => 'button fr')); ?>
									<?=$this->form->error('terms'); ?>
									</div>
								<?=$this->form->end(); ?>

								<div>
								<?php if ($userfb): ?>
								    <h2> <img src="https://graph.facebook.com/<?=$userfb['id']?>/picture"> <br/> Hi <?=$userfb['name']?> - you're one step away from joining with Facebook</h2>
                                <?php else: ?>
                                     <h3 style="color:#999; font-size:18px;">Register With Facebook</h3>
                                    <hr />
                                    <fb:login-button perms="email,publish_stream, offline_access" size="large" length="long" v="2" style="text-align:center;">Register With Facebook</fb:login-button>
                                <?php endif;?>
								</div>
								</div>
								<div class="bl"></div>
								<div class="br"></div>
							</div>
							<div class="r-container clear reg-list">
								<div class="tl"></div>
								<div class="tr"></div>
								<div class="r-box lt-gradient-1">
									<strong class="red">Why you will love Totsy</strong>
									<ul class="bugs columns-2">
										<li>Exclusive sales for kids, moms and families</li>
										<li>Sales last up to 3 days, plenty of time to shop.</li>
										<li>Savings of up to 90% off retail.</li>
										<li>A tree is planted for your first purchase.</li>
										<li>Membership is free</li>
										<li>We are 100% green.</li>
									</ul>
								</div>
								<div class="bl"></div>
								<div class="br"></div>
							</div>
							<p class="login-sig clear">Exclusive access, Top brands. Great deals. <span class="red">The savvy mom shops at Totsy.com</span></p> 
							<p style="text-align:center; font-size:11px; color:#333;">* Offer expires 30 days after registration</p>
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

	<ul>
			<li class="first"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/aboutus" title="About Us">About Us</a></li>
			<li><a href="http://blog.totsy.com" title="Blog" target="_blank">Blog</a></li>
			<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
			<li><a href="/pages/affiliates" title="Affiliates">Affiliates</a></li>
			
			<! -- switch where this link points depending on whether they're logged in or not --> 
			<?php if (empty($userInfo)){ ?>
			<li><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
			<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
			<?php } else { ?>
			<li><a href="/tickets/add" title="Contact Us">Contact Us</a></li>
			<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li> 
			<?php } ?>
		</ul>

	<span id="copyright">&copy; 2011 Totsy.com. All Rights Reserved. <br />10 West 18th Street, Floor 4 - New York, NY 10011</span>

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