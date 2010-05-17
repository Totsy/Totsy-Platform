


<script type="text/javascript">
<!--
	Window.onDomReady(function(){
		document.formvalidator.setHandler('passverify', function (value) { return ($('password').value == value); }	);
	});
// -->

window.addEvent('domready', function(){equalHeights('.col');});

</script>

<div class="modal-top">
<h1 class="white"><?php echo 'Register with Totsy';?></h1>
</div>

<div class="reg-left logo-bot col">
		
	<div class="reg-copy">
	
		<h2><?php echo ('Join Totsy');?></h2>
		<p>the leading source for today's top kids designers at up to 70% off retail. Gain access to brands for child, boys, girls at insider prices.</p>
		
		<p><?php echo ('Exclusive Access. Top Brands. Great Deals.');?><br />
		<span class="red lg"><?php echo ('The savvy mom shops at Totsy.com.');?></span>
		</p>
		
		<blockquote>
			"People with a taste for high-end green fashion items at deep discount have said yes to Totsy addiction."<br />
			<strong>The New York Times</strong>
		</blockquote>
		
		<blockquote>
			"The online shopping phenomenon."<br />
			<strong>Vogue</strong>
		</blockquote>
		
		<a href="#" title="Sign In"><?php echo ('Already a member? Sign in here.');?></a>
		
	</div>
	
</div>

<div class="reg-right col">
	<div class="reg-copy">		
			<fieldset>
			
				<legend class="no-show">Registration</legend>
				
				<label for="fname" class="label"><?php echo ('First Name');?></label>
				<input type="text" name="fname" id="fname" class="inputbox" value="" />
				
				<label for="lname" class="label"><?php echo ('Last Name');?></label>
				<input type="text" name="lname" id="lname" class="inputbox" value="" />
				
				<label for="email" class="label"><?php echo ('Email');?></label>
				<input type="text" name="email" id="email" class="inputbox" value="" />
				
				<label for="email" class="label"><?php echo ('User Name');?></label>
				<input type="text" id="username" name="username" size="40" class="inputbox required validate-username" maxlength="25" />
				
				<label for="password" class="label"><?php echo ('Password (6 Characters of more)');?></label>
				<input class="inputbox required validate-password" type="password" id="password" name="password" size="40" value="" />
				<input class="inputbox required validate-passverify" type="password" id="password2" name="password2" size="40" value="" />

				<label class="clear block sm"><input type="checkbox" name="terms" id="terms" /> <span>I agree to the <a href="#" title="Agree to the Terms of service">terms of service</a></span></label>
				
				<button type="submit" name="submit" id="submit-btn" class="btn reg-btn"><?php echo ('Register');?></button>
				
				<span class="sm">We value your <a href="#" title="Privacy Policy">privacy</a>. Totsy will not sell or rent your email address to third parties.</span>
				
			</fieldset>
				<input type="hidden" name="task" value="register_save" />
				<input type="hidden" name="id" value="0" />
				<input type="hidden" name="gid" value="0" />
	
	</div>

</div>