<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.backstretch.min.js');?>

<?php

use \DirectoryIterator;
use lithium\net\http\Media;
$images = array();
$imgDirectory = $this->_request->env('base') . '/img/login/';

/**
 * Get a random login image (of type jpg or png).
 */
foreach (new DirectoryIterator(Media::webroot(true) . '/img/login') as $file) {
	if ($file->isDot() || !preg_match('/\.(png|jpg)$/', $file->getFilename())) {
		continue;
	}
	$images[] = $file->getFilename();
}
$image = $images[array_rand($images)];


?>


<script type="text/javascript">
   
    jQuery(document).ready(function($){
    
    	$.backstretch("<?=$imgDirectory . $image;?>");
    
    });
    
</script>

<div id="fullscreen">
	
	<div id="login-box">
	
		<div id="login-box-border" class="login-modal">
			
			<div id="login-box-container">
				
				<div class="tt">
					<div></div>
				</div>
				
				<div class="tm">
					<div class="ti">		
					
						<div class="tc login-inner">
				
							<h1 id="logo">Totsy</h1>
							
							<div id="intro-copy">
								<p class="red">You have places to be, things to do, and little ones in tow.</p>
								<p>At Totsy, moms on the go and moms to be experience the shopping they desire at prices they can't resist.</p>
							</div>
							<?php if ($success == false): ?>
							<div class="sign-in-container">
								<div id="sign-in-box" class="r-container">
									<div class="tl"></div>
									<div class="tr"></div>
									<div class="r-box lt-gradient-1">
										
											<h3>Please enter your email and further instructions will be provided.</h3>
											<?=$this->form->create(null, array('id'=>'loginForm')); ?>
												<center>
													<br><br>
													<?=$this->form->text('email', array('label' => 'Email Address')); ?>
													<br><br>
													<?=$this->form->submit('Reset Password',array(
													'class'=>"reset-pw-btn"
													));?>
												</center>
											<?=$this->form->end(); ?>
                                            
                                                                                        										
									</div>
									<div class="bl"></div>
									<div class="br"></div>
								</div>
							</div>	
							<?php endif ?>
							<div class="register-container">
								<div id="register-box" class="r-container">
									<?php if (!empty($message)): ?>
										<div id='message' class="cart-message"><?=$message;?> <a href="/" class="md" title="Return to Totsy">Return to Totsy</a></div>
									<?php endif ?>
								</div>
							</div>
							
							<div class="clear"><!-- --></div>
				
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

	<ul>
		<li class="first"><a href="#" title="Terms of Use">Terms of Use</a></li>
		<li><a href="#" title="Privacy Policy">Privacy Policy</a></li>
		<li><a href="#" title="About Us">About Us</a></li>
		<li><a href="#" title="FAQ">FAQ</a></li>
		<li class="last"><a href="#" title="Contact Us">Contact Us</a></li>
	</ul>
	
	<span id="copyright">&copy; 2010 Totsy.com. All Rights Reserved.</span>

</div>