<script> 
<!-- 

document.getElementById('registerButton').addEventListener('click', onRegisterSubmit, false);
document.getElementById('registerButton2').addEventListener('click', onRegisterSubmit, false);
var regFlag = false;
function showNewMemberForm(){
	document.getElementById('confirm-container').removeClassName('show');
	document.getElementById('confirm-bottom').removeClassName('show');
	document.getElementById('confirm-top').removeClassName('show');
	document.getElementById('newmember-container').addClassName('show');
	document.getElementById('newmember-top').addClassName('show');
	document.getElementById('newmember-bottom').addClassName('show');

	regFlag = true;
}
function showConfirmForm(){
	document.getElementById('newmember-container').removeClassName('show');
	document.getElementById('newmember-bottom').removeClassName('show');
	document.getElementById('newmember-top').removeClassName('show');
	document.getElementById('confirm-container').addClassName('show');
	document.getElementById('confirm-top').addClassName('show');
	document.getElementById('confirm-bottom').addClassName('show');
	regFlag = false;
}

function onRegisterSubmit(evt){
	
	if(validateRegisterForm()){
	
		document.getElementById('register-load-indicator').addClassName('show');
	
		var ajax = new Ajax();
		var reply;
		ajax.resonseType = Ajax.FBML;
		ajax.ondone = function(data){
			console.log(data);
			if(data == "1")
			{
				reply = '<div id="response" class="success"><p style="font-weight:bold">Thank you for your submission with Totsy.com\'s Mom of the Week.</p></div>';
			} else {
				if (regFlag == true){
					reply = '<div id="response" class="error"><p style="font-weight:bold">This email is already registered at Totsy.com</p></div>';
				} else {
					reply = '<div id="response" class="error"><p style="font-weight:bold">Your sweepstakes submission did not make it to our servers at Totsy. Please try again later.</p></div>';
				}
				
			}
			
			document.getElementById("response-container").setInnerXHTML(reply);
			document.getElementById('register-load-indicator').removeClassName('show');
			
			document.getElementById('response-container').addClassName('show');
			document.getElementById('response-top').addClassName('show');
			document.getElementById('response-bottom').addClassName('show');
	
			if(document.getElementById('response').hasClassName('success')){
				document.getElementById('register-form-container').removeClassName('show');
				document.getElementById('register-bottom').removeClassName('show');
				document.getElementById('register-top').removeClassName('show');
				
				document.getElementById('confirm-container').removeClassName('show');
				document.getElementById('confirm-bottom').removeClassName('show');
				document.getElementById('confirm-top').removeClassName('show');
				
				document.getElementById('newmember-container').removeClassName('show');
				document.getElementById('newmember-bottom').removeClassName('show');
				document.getElementById('newmember-top').removeClassName('show');
				//document.getElementById('success-container').addClassName('show');
			}
		}
		if(regFlag == true){
			ajax.post("http://www.totsy.com/momoftheweek", {
				"q1":document.getElementById("form[question][question1]").getValue(),
				"q2":document.getElementById("form[question][question2]").getValue(),
				"q3":document.getElementById("form[question][question3]").getValue(),
				"q4":document.getElementById("form[question][question4]").getValue(),
				"q5":document.getElementById("form[question][question5]").getValue(),
				"firstname":document.getElementById("form[register][firstName]").getValue(),
				"lastname":document.getElementById("form[register][lastName]").getValue(),
				"email":document.getElementById("form[register][email]").getValue(),
				"confirmemail":document.getElementById("form[register][email]").getValue(),
				"password":document.getElementById("form[register][password1]").getValue(),
				"terms":"1"
			});	
		} else {
			ajax.post("http://www.totsy.com/momoftheweek", {
				"q1":document.getElementById("form[question][question1]").getValue(),
				"q2":document.getElementById("form[question][question2]").getValue(),
				"q3":document.getElementById("form[question][question3]").getValue(),
				"q4":document.getElementById("form[question][question4]").getValue(),
				"q5":document.getElementById("form[question][question5]").getValue(),
				"email":document.getElementById("form[confirm][email]").getValue()
			});	
		}
	}
	
	return false;
}


function validateRegisterForm(){
	var valid = true;
	
	var q1 = document.getElementById("form[question][question1]");
	if(q1.getValue().length < 2){
		q1.addClassName('error');
		document.getElementById('q1-error').addClassName('show');
		valid = false;
	}else{
		q1.removeClassName('error');
		document.getElementById('q1-error').removeClassName('show');
	}
	
	var q2 = document.getElementById("form[question][question2]");
	if(q2.getValue().length < 2){
		q2.addClassName('error');
		document.getElementById('q2-error').addClassName('show');
		valid = false;
	}else{
		q2.removeClassName('error');
		document.getElementById('q2-error').removeClassName('show');
	}
	
	/*
var q3 = document.getElementById("form[question][question3]");
	if(q3.getValue().length < 2){
		q3.addClassName('error');
		document.getElementById('q3-error').addClassName('show');
		valid = false;
	}else{
		q3.removeClassName('error');
		document.getElementById('q3-error').removeClassName('show');
	}
*/
	
	var q4 = document.getElementById("form[question][question4]");
	if(q4.getValue().length < 2){
		q4.addClassName('error');
		document.getElementById('q4-error').addClassName('show');
		valid = false;
	}else{
		q4.removeClassName('error');
		document.getElementById('q4-error').removeClassName('show');
	}
	
	var q5 = document.getElementById("form[question][question5]");
	if(q5.getValue().length < 2){
		q5.addClassName('error');
		document.getElementById('q5-error').addClassName('show');
		valid = false;
	}else{
		q5.removeClassName('error');
		document.getElementById('q5-error').removeClassName('show');
	}
	
	var q6 = document.getElementById("form[question][question6]");
	if(q6.getValue().length < 2){
		q6.addClassName('error');
		document.getElementById('q6-error').addClassName('show');
		valid = false;
	}else{
		q6.removeClassName('error');
		document.getElementById('q6-error').removeClassName('show');
	}

	if(regFlag == true){
		var fName = document.getElementById("form[register][firstName]");
		if(fName.getValue().length < 2){
			fName.addClassName('error');
			document.getElementById('fn-error').addClassName('show');
			valid = false;
		}else{
			fName.removeClassName('error');
			document.getElementById('fn-error').removeClassName('show');
		}
		
		var lName = document.getElementById("form[register][lastName]");
		if(lName.getValue().length < 2){
			lName.addClassName('error');
			document.getElementById('ln-error').addClassName('show');
			valid = false;
		}else{
			lName.removeClassName('error');
			document.getElementById('ln-error').removeClassName('show');
		}
		
		var email = document.getElementById("form[register][email]");
		//var emailConf = document.getElementById("form[register][emailConf]");
		if(!validateEmail(email.getValue())){
			document.getElementById('email-error').addClassName('show');
			email.addClassName('error');
			valid = false;
		}else{
			document.getElementById('email-error').removeClassName('show');
			email.removeClassName('error');
		}
		
		var password = document.getElementById("form[register][password1]");
		if(password.getValue().length < 4){
			password.addClassName('error');
			document.getElementById('pw-error').addClassName('show');
			valid = false;
		}else{
			password.removeClassName('error');
			document.getElementById('pw-error').removeClassName('show');
		}
		
		/*
var terms = document.getElementById("form[register][terms]");
		if(terms.checked){
			terms.removeClassName('error');
			document.getElementById('terms-error').removeClassName('show');
		}else{
			terms.addClassName('error');
			document.getElementById('terms-error').addClassName('show');
			valid = false;
		}
*/
		
		

	} else {
		var email = document.getElementById("form[confirm][email]");
		if(!validateEmail(email.getValue())){
			email.addClassName('error');
			document.getElementById('confirmemail-error').addClassName('show');
			valid = false;
		}else{
			email.removeClassName('error');
			document.getElementById('confirmemail-error').removeClassName('show');
		}

	}
		
	return valid;	
}

function validateEmail(elementValue){
   var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
   return emailPattern.test(elementValue);
}

function output(val){
	document.getElementById("output").setTextValue(val);
}

//--> 
</script> 

<div style="height:1410px;">
	<div style="position: absolute; left:0px; width:520px; background-image: url(http://www.totsy.com/img/motw/images/facebook_motw_bg.jpg); overflow-x:hidden">

	  <div id="body">			
		<link rel="stylesheet" href="http://www.totsy.com/css/motw/reset.css" type="text/css" media="screen" />
		<style>
		/* @group Global */

		html{
			width: 520px;
			height: auto;
		}
		
		#body{
			background: transparent;
			font-family: Arial;
			font-size: 13px;
			color: #fff;
			width: auto;
		}
		
		#container{
			width: auto;
			height: auto;
			background: transparent;
		}
		
		
		p{
			font-size: 13px;
			line-height: 18px;
			font-family: Arial;
			font-size: 13px;
			display: block;
		}
		
		a{
			color: red;
			cursor: pointer;
		}
		
	
		.clear{
			clear: both;
		}
		
		.h-dots {
			background: transparent url(http://www.totsy.com/img/motw/images/h-dots.png) repeat-x;
			height: 1px !important;
		}
		
		.fb-like-button{
			width: 53px;
			height: 25px;
			overflow: hidden;
		}
		
		.fb-container.share{
			margin: 20px 0;
			background-color: #ddd;
			padding: 10px;
			border: 1px solid #ccc;
		}
		
		.fb-container.invite{
			width: 440px;
			overflow: hidden;
		}
		
		.form-header{
			margin-bottom: 12px;
		}
		
		.form-header img {
		vertical-align: text-bottom;
		}
		/* @group Header */
		
		#header{
			width: 100%;
			background:transparent;
			padding: 10px 0 0 48px;
			height: 62px;
		}
		
		#header ul.navigation{
			float: right;
		}
		
		#header ul.navigation li{
			float: left;
			margin: 10px 10px;	
		}
		
		.top_curve {
			width: 423px;
			height:9px;
			background: url(http://www.totsy.com/img/motw/images/curve_top.png);
			display: none;
		}
		.bottom_curve {
			width: 423px;
			height:9px;
			background: url(http://www.totsy.com/img/motw/images/curve_bottom.png);
			display: none;
		}
		
		/* @end */
		
		/* @group Style Expressed */
		
		#intro{
			width: 100%;
			background-image: url(http://www.totsy.com/img/motw/images/messaging_bg.png);
			margin: 0 0 0 0;
			padding-left: 54px;
			color:#ffffff;
			height: 25px;
			line-height: 25px;
			text-transform: capitalize;
			font-size: 14px;
		}
		
		#intro img{
			margin: 0 0 10px 0;
		}
		
		/* @end */
		
		/* @end */
		
		/* @group Register */
		
		/* @group Register */
		
		#register{
			width: auto;
			margin: 0 0px 0px 0;
			color: #000;
			padding: 20px 54px 20px 54px;
			display: block;
		}
		
		#register h3{
			color: red;
			font-size: 24px;
			font-family: Arial;
			margin-bottom: 20px;
		}
		
		#register h4{
			color: red;
			font-size: 24px;
			font-family: Arial;
			margin-bottom: 8px;
		}
		
		#register .container.show{
			display: block;
		}
		
		#register #register-container{
			display: none;
			
		}
		
		#register-form-container, #newmember-container, #confirm-container, #response-container {
			background:url(http://www.totsy.com/img/motw/images/trans_bg_middle.png);
			padding: 20px;
			width:383px;
		}
		
		#register #register-container.show{
			display: block;
		}
		
		#register #register-form div,
		#register #question-form div{
			margin: 0 0 10px 0;
			display: block;
		}
		
		
		#register #register-form p.agree{
			display: inline;
			text-indent: 0px;
			width: auto;
			height: auto;
			font-size: 10px;
			color: #666;
			text-align: center;
		}
		
		#register #register-form p.agree a{
			color: #666;	
		}
		
		.important{
			color: #666;
			font-size: 12px;
			font-weight: bold;	
		}
		
		#register input,
		#register #register-form select{
			height: auto;
			display: block;
			margin: 0;
			padding: 5px;
			border:1px solid #999;
		}
		
		#register input.checkbox {
			height: auto;
			display: inline !important;
			margin: 0;
			padding: 0;
			border:none;
		}

		
		.short{
		width:174px;
		}
		
		.long{
		width:370px;
		}
		
		#register #register-form input.error,
		#register #register-form select.error,
		#register #confirm-form input.error,
		#register #confirm-form select.error,
		#register #question-form input.error,
		#register #question-form select.error{
			border-color: #ec008c;
		}
		
		.error-text {
		color:red;
		display:none;
		}
		
		.error-text.show {
		display: block;
		}
		
		#register #register-form input.agree{
			width: auto;
			margin: 5px 10px 0 0;
		}
		
		#register #register-tools input.register-button{
			width: 129px;
			height: 27px;
			border: 0;
			background: none;
			margin: 0;
			padding: 0;
			font-size: 0;
			text-indent: -10000px;
			background: transparent url(http://www.totsy.com/img/motw/images/enter_contest_button.png) no-repeat 0 0;
			cursor: pointer;
		}
		
		
		#register #register-tools #register-load-indicator{
			float: left;
			margin: 0 0 0 10px;
			top: -8px;
			position: relative;
			display: none;
		}
		
		#register #register-tools #register-load-indicator.show{
			display: block;	
		}
		
		/* @group Response */
		
		#response-container{
			width: auto;
		}
		
		#response{
			padding: 10px;
			margin: 0 0 10px 0;
			display: none;
		}
		
		#response.error{
			color: #cc0000;
			display: block;
		}
		
		#response.success{
			color: #189212;
			display: block;
		}
		
		/* @end */
		/* @end */
		
		/* @group Success */
		
		#register #register-form-container{
			display: none;
		}
		
		#register #register-form-container.show{
			display: block;	
		}

		
		#register #response-container{
			display: none;
		}
		
		#register #response-container.show{
			display: block;	
		}
		
		#register #success-container{
			display: none;
		}
		
		#register #success-container.show{
			display: block;	
		}
		
		#register #confirm-container{
			display: none;
		}
		
		#register #confirm-container.show{
			display: block;	
		}
		
		#register #newmember-container{
			display: none;
		}
		
		#register #newmember-container.show{
			display: block;	
		}
		
		#register #success-container h2{
			text-transform: uppercase;
			font-size: 24px;
			margin: 0 0 20px 0;	
		}
		
		#register #success-container h2 a{
			display: block;
			text-decoration: underline;
		}
		
		#fb-friend-selector{
			margin: 20px 0;
		}
		
		#fb-friend-selector p {
			font-family: Arial;
			color: #333;
			font-size: 12px;
			font-weight: bold;
		}
		
		#register #success-container p.disclaimer{
			color: #999;
			font-size: 10px;
			margin: 20px 0 0 0;
			display: block;
			clear: both;
		}
		
		
		
		/* @end */
		
		
		
		/* @end */
		
		/* @group Footer */
		
		#footer{
			margin: 10px 0 0 0;
			width: 520px;
		}
		
		#footer h2{
			width: 480px;
			display: block;
			padding: 0 0 10px 0;
			border-bottom: 1px solid #666;
			margin: 0 0 20px 0;
		}
		
		#footer .footer-items{
			padding: 0 0;
		}
		
		#footer .footer-items .footer-item{
			float: left;
			margin: 0 15px 15px 0;
		}
		
		#footer .footer-items .footer-item img{
			width: 150px;
		}	
		
		#footer .footer-items .footer-item.last{
			margin: 0 0 0 0;
		}
		/* @end */
		
		/* @group Promo */
		
		#promo-container{
			padding: 0 20px;
		}
		
		/* @end */

		</style>
	
		<div id="output"></div>
		
		<div id="container">
					
			<div id="header">
				<img src="http://www.totsy.com/img/motw/images/motw_logo.png" alt="Mom Of The Week" border="0" title="Mom Of The Week">
			</div>
			<div id="intro">
				We Pick A Winner Each Week.
			</div>
			
			<div id="register">
	<!-- 	Share with Friends	 -->
				<div id="success-container" class="container">
					<div id="fb-friend-selector">
					
						<p>Select friends below to share Mom of the Week and earn a $15 credit</p>
					
						<fb:request-form 
							action="http://apps.facebook.com/instant_access_in/index.php?" 
							method="POST" 
							invite="false" 
							type="Mom of the Week from Totsy.com" 
							content="something here about Totsy <?php echo htmlentities('<fb:req-choice url="http://apps.facebook.com/momoftheweek/mom" label="Mom of the Week" />'); ?>"
							
						>
							<fb:multi-friend-selector 
								condensed="true"
								selected_rows="10"
								unselected_rows="10"
							/>
							
							<fb:request-form-submit label="Send"/>
						</fb:request-form>
					</div>
					<p class="disclaimer">$15.00 credit for each friend that makes a purchase</p>
				</div>
	<!-- 			End friend selector			 -->
				
				<div id="response-top" class="top_curve container"></div>
				<div id="response-container" class="container"></div>
				<div id="response-bottom" class="bottom_curve container"></div>
			
			
	<!-- START REG -->
				<div id="register-container" class="container show">
					<div id="register-top" class="top_curve container show"></div>
					<div id="register-form-container" class="container show">
					
					<div class="form-header"><img src="http://www.totsy.com/img/motw/images/enter_header.png" alt="enter_header"/></div>
						<form name="question" id="question-form" method="post">
							<div style="margin-top:8px; float:left;">
								<p class="firstName important">What is your full name?</p><span id="q1-error" class="error-text">Please answer this question.</span>
								<input class="short" type="text" name="form[question][question1]" id="form[question][question1]" value="" onclick="return false;"/>
								<div class="clear"></div>
							</div>
							
							<div style="float:left; margin-top:8px; margin-left:10px;">
								<p class="firstName important">What is your zipcode?</p><span id="q2-error" class="error-text">Please answer this question.</span>
								<input class="short" type="text" name="form[question][question2]" id="form[question][question2]" value="" onclick="return false;"/>
								<div class="clear"></div>
							</div>
							
							<div class="clear h-dots"></div>
							
							<div>
								<span id="q3-error" class="error-text">Please answer this question.</span>
								
								<span style="float:left" class="firstName important">How many kids do you have?</span>
								<select name="form[question][question3]" id="form[question][question3]" value="" onchange="return false;" class="short" style="float:right;">
									<option value="0">I don't have any children</option>
									<option value="1" selected="selected">I have 1 child</option>
									<option value="2">I have 2 children</option>
									<option value="3">I have 3 children</option>
									<option value="4">I have 4 children</option>
									<option value="5">I have 5 children</option>
									<option value="6">I have 6 children</option>
									<option value="7">I have 7 children</option>
									<option value="8">I've got a lot of kids!</option>		
								</select>
								<div class="clear" style="height:2px;"></div>
							</div>
							
							<div class="clear h-dots"></div>
							
							<div>
								<p class="firstName important">Tell us why you should be Mom of the Week</p><span id="q4-error" class="error-text">Please answer this question.</span>
								<textarea class="long clear" style="height:65px;" name="form[question][question4]" id="form[question][question4]" value="" onclick="return false;"/></textarea>
								<div class="clear"></div>
							</div>
							
							<div class="clear">
								<p class="firstName important">What is your favorite blog?</p><span id="q5-error" class="error-text">Please answer this question.</span>
								<input class="long" type="text" name="form[question][question5]" id="form[question][question5]" value="" onclick="return false;" placeholder="http://example.com"/>
								<div class="clear"></div>
							</div>
							
							<div class="clear">
								<p class="firstName important">What is your favorite parenting website?</p><span id="q6-error" class="error-text">Please answer this question.</span>
								<input class="long" type="text" name="form[question][question6]" id="form[question][question6]" value="" onclick="return false;" placeholder="http://example.com"/>
								<div class="clear"></div>
							</div>

							
							
						</form>
					</div>
					<div id="register-bottom" class="bottom_curve container show"></div>
					
					<div id="confirm-top" class="top_curve container show" style="margin-top:20px;"></div>
					<div id="confirm-container" class="container show">
					<div class="form-header"><img src="http://www.totsy.com/img/motw/images/confirmmember_register.png" alt="enter_header"/>&nbsp;&nbsp;&nbsp;Not a member? <a onclick="showNewMemberForm()">Click Here</a>.</div>
						<form name="confirm" id="confirm-form" method="post">
							<div style="margin-top:8px;">
								<p>Email address used to sign up for Totsy.com</p><span id="confirmemail-error" class="error-text">Please enter a valid email address.</span>
								<input class="long" type="text" name="form[confirm][email]" id="form[confirm][email]" value="" onclick="return false;"/>
								<div class="clear"></div>
							</div>
						</form>
						<div style="clear:both; height:10px; margin:0"></div>
						<div id="register-tools" class="container show">
							<span id="terms-error" class="error-text">Please accept the Terms in order to register.</span>
							<div>
								<div style="float:left;" class="short"><input style="float:left;" type="submit" value="Register" title="Register Now" alt="Register Now" class="register-button" id="registerButton" onclick="return false;"/></div>
								<div style="float:left;font-size:10px; margin-top:7px;"><input class="checkbox" type="checkbox" name="form[register][terms]" id="form[register][terms]" />&nbsp;&nbsp;&nbsp;&nbsp;I accept the Terms of Service</div>
								<div class="clear"></div>
							</div>
	
							<div id="register-load-indicator">
								<img src="http://www.totsy.com/img/motw/ajax-loader.gif">
							</div>
						</div>		
					</div>
					<div id="confirm-bottom" class="bottom_curve container show"></div>
					
					<div id="newmember-top" class="top_curve container" style="margin-top:20px;"></div>
					<div id="newmember-container" class="container"> 
					<div class="form-header"><img src="http://www.totsy.com/img/motw/images/becomemember_register.png" alt="enter_header"/>&nbsp;&nbsp;&nbsp;Already a member? <a onclick="showConfirmForm()">Click Here</a>.</div>
						<form name="register" id="register-form" method="post">
						
						<div style="margin-top:8px; float:left;">
							<p class="firstName important">First Name</p><span id="fn-error" class="error-text">This field is required</span>
							<input class="short" type="text" name="form[register][firstName]" id="form[register][firstName]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div style="float:left; margin-top:8px; margin-left:10px;">
							<p class="lastName important">Last Name</p><span id="ln-error" class="error-text">This field is required</span>
							<input class="short" type="text" name="form[register][lastName]" id="form[register][lastName]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div style="clear:both; height:1px;"></div>
						
						
						
						<div style="float:left;">
							<p class="firstName important">Email Address</p><span id="email-error" class="error-text">Please enter an email.</span>
							<input class="short" type="text" name="form[register][email]" id="form[register][email]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
												
						<div style="float:left;  margin-left:10px;">
							<p class="password important">Password</p><span id="pw-error" class="error-text">This field is required</span>
							<input class="short" type="password" name="form[register][password1]" id="form[register][password1]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						<div style="clear:both; height:1px;"></div>
						
					</form>
						<div id="register-tools" class="container show">
							<span id="terms-error" class="error-text">Please accept the Terms in order to register.</span>
							<div>
								<div style="float:left;" class="short"><input style="float:left;" type="submit" value="Register" title="Register Now" alt="Register Now" class="register-button" id="registerButton2" onclick="return false;"/></div>
								<div style="float:left;font-size:10px; margin-top:7px;"><input class="checkbox" type="checkbox" name="form[register][terms]" id="form[register][terms]" />&nbsp;&nbsp;&nbsp;&nbsp;I accept the Terms of Service</div>
								<div class="clear"></div>
							</div>

	
							<div id="register-load-indicator">
								<img src="http://www.totsy.com/img/motw/ajax-loader.gif">
							</div>
						</div>		
					</div>	
					<div id="newmember-bottom" class="bottom_curve container"></div>
									
							
							
					<div class="clear"></div>
				</div>
				
			</div>
											
		</div>
	
		<div id="footer">
		<img src="http://www.totsy.com/img/preview-mothw-fbml.jpg" alt="Mom Of The Week Prizes"/>
	
		</div>
		
		<div class="clear"></div>
		</div>
		</div>
	</div>
</div>
