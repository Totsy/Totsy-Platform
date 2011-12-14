<div id="fullscreen" style:"z-index: 200;">
	
	<div id="login-box">
	
		<div id="login-box-border">
			
			<div id="login-box-container">
			
				<h1 id="logo">Totsy</h1>
				
				<div id="intro-copy">
				
					<p class="red">You have places to be, things to do, and little ones in tow.</p>
					
					<p>At Totsy, moms on the go and moms to be experience the shopping they desire at prices they can't resist.</p>
				
				</div>
				
				<div class="sign-in-container">
					<div id="sign-in-box" class="r-container">
							<h2>Hi <?php echo $user->firstname?></h2>
							<p>We just noticed that you are browsing Totsy with an older version of Internet Explorer. <br><br>We want you to fully enjoy your shopping here at Totsy and are asking that you upgrade to the latest version of Internet Explorer. We apologize for the inconvenience. <br><strong><?php echo $this->html->link('Please click here to get the latest version.', 'http://www.microsoft.com/windows/internet-explorer/default.aspx'); ?></strong></p>
							
					</div>
				</div>
				
				
				<p class="login-sig clear">Exclusive access, Top brands. Great deals. <span class="red">The savvy mom shops at Totsy.com</span></p>
			
			</div>
			
		</div>
		
	</div>
		
</div>

<div id="footer">
	<?php echo $this->view()->render(array('element' => 'footerNavPublic')); ?>
</div>