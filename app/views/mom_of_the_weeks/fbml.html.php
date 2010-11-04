<script> 
<!-- 

document.getElementById('registerButton').addEventListener('click', onRegisterSubmit, false);
var regFlag = false;
function showNewMemberForm(){
	document.getElementById('confirm-container').removeClassName('show');
	document.getElementById('newmember-container').addClassName('show');
	regFlag = true;
}
function showConfirmForm(){
	document.getElementById('newmember-container').removeClassName('show');
	document.getElementById('confirm-container').addClassName('show');
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
				reply = '<div id="response" class="success"><p>Thank you for your submission with Totsy.com\'s Mom of the Week.</p></div>';
			} else {
				if (regFlag == true){
					reply = '<div id="response" class="error"><p>This email is already registered at Totsy.com</p></div>';
				} else {
					reply = '<div id="response" class="error"><p>Your sweepstakes submission did not make it to our servers at Totsy. Please try again later.</p></div>';
				}
				
			}
			
			document.getElementById("response-container").setInnerXHTML(reply);
			document.getElementById('register-load-indicator').removeClassName('show');
			if(document.getElementById('response').hasClassName('success')){
				//document.getElementById('register-container').removeClassName('show');
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
				"q6":document.getElementById("form[question][question6]").getValue(),
				"firstname":document.getElementById("form[register][firstName]").getValue(),
				"lastname":document.getElementById("form[register][lastName]").getValue(),
				"email":document.getElementById("form[register][email]").getValue(),
				"confirmemail":document.getElementById("form[register][emailConf]").getValue(),
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
				"q6":document.getElementById("form[question][question6]").getValue(),
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
	
	var q3 = document.getElementById("form[question][question3]");
	if(q3.getValue().length < 1){
		q3.addClassName('error');
		document.getElementById('q3-error').addClassName('show');
		valid = false;
	}else{
		q3.removeClassName('error');
		document.getElementById('q3-error').removeClassName('show');
	}
	
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
		var emailConf = document.getElementById("form[register][emailConf]");
		if(!validateEmail(email.getValue()) || email.getValue() != emailConf.getValue()){
			document.getElementById('email-error').addClassName('show');
			email.addClassName('error');
			emailConf.addClassName('error');
			valid = false;
		}else{
			document.getElementById('email-error').removeClassName('show');
			email.removeClassName('error');
			emailConf.removeClassName('error');
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

<div>
    <div style="height: 1200px;">
        <!-- start non-fan part -->
        <div class="top">
            <img src="http://www.totsy.com/img/motw/nonfan.jpg" width="520" height="353" alt="Become a fan to enter the sweepstakes" />
        </div>
        <!-- end non-fan part -->

         <fb:fbml version="1.1">
         <fb:visible-to-connection>
            <div style="position: absolute; top:2px;left:0px; width:100%; background-color:white">

   <div id="body">			
	<link rel="stylesheet" href="http://www.totsy.com/css/motw/reset.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="http://www.totsy.com/css/motw/style.css?v=1.47" type="text/css" media="screen" />

	<div id="output"></div>
	
	<div id="container">
	
		<div id="response-container">
		
		</div>
				
		<div id="header">
			<img src="http://www.totsy.com/img/motw/MotW_logo.jpg" alt="Mom Of The Week" border="0" title="Mom Of The Week">
		</div>
		<div id="intro">
			Four Moms each month! Over $1,000 Dollars in Prizes! Get featured!	
		</div>

		<div id="footer">
		<img src="http://www.totsy.com/img/motw/MotW_prizing_wk1.jpg" alt="MotW_prizing_wk1" width="520" height="192" />

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

<!-- START REG -->
			<div id="register-container" class="container show">
								
				<h3>Enter Our Weekly Contest:</h3>
								
				<div id="register-form-container">
					<form name="question" id="question-form" method="post">
						<div>
							<p class="firstName important">1. Your full name:</p><span id="q1-error" class="error-text">Please answer this question.</span>
							<input type="text" name="form[question][question1]" id="form[question][question1]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div>
							<p class="firstName important">2. Your zip code:</p><span id="q2-error" class="error-text">Please answer this question.</span>
							<input type="text" name="form[question][question2]" id="form[question][question2]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div>
							<p class="firstName important">3. How many kids do you have?</p><span id="q3-error" class="error-text">Please answer this question.</span>
							<input type="text" name="form[question][question3]" id="form[question][question3]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div>
							<p class="firstName important">4. Tell us why you should be Mom of the Week:</p><span id="q4-error" class="error-text">Please answer this question.</span>
							<input type="text" name="form[question][question4]" id="form[question][question4]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div>
							<p class="firstName important">5. What is the URL of your favorite blog?</p><span id="q5-error" class="error-text">Please answer this question.</span>
							<input type="text" name="form[question][question5]" id="form[question][question5]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>

						<div>
							<p class="firstName important">6. What is the URL of your favorite parenting web site?</p><span id="q6-error" class="error-text">Please answer this question.</span>
							<input type="text" name="form[question][question6]" id="form[question][question6]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>

					</form>
				</div>
				
				<div id="confirm-container" class="container show" style="margin-top:20px;">
					<h4>Only Members Can Participate.<br />Confirm Membership:</h4>Not a member? <a onclick="showNewMemberForm()">Click Here</a>.
					<form name="confirm" id="confirm-form" method="post">
						<div style="margin-top:20px;">
							<p>Enter the email address used to access your Totsy account:</p><span id="confirmemail-error" class="error-text">Please enter a valid email address.</span>
							<input type="text" name="form[confirm][email]" id="form[confirm][email]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
					</form>
				</div>
				
				<div id="newmember-container" class="container" style="margin-top:20px;">
				<h4>Register for Totsy to Enter:</h4>Already a member? <a onclick="showConfirmForm()">Click Here</a>.
					<form name="register" id="register-form" method="post">
						
						<div style="margin-top:20px;">
							<p class="firstName important">First Name</p><span id="fn-error" class="error-text">This field is required</span>
							<input type="text" name="form[register][firstName]" id="form[register][firstName]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div>
							<p class="lastName important">Last Name</p><span id="ln-error" class="error-text">This field is required</span>
							<input type="text" name="form[register][lastName]" id="form[register][lastName]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div>
							<p class="email important">Email Address</p><span id="email-error" class="error-text">Please make sure that these fields are filled out and match</span>
							<input type="text" name="form[register][email]" id="form[register][email]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div>
							<p class="email important">Confirm Email Address</p><span id="email-error" class="error-text">Please make sure that these fields are filled out and match</span>

							<input type="text" name="form[register][emailConf]" id="form[register][emailConf]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>

						
						<div>
							<p class="password important">Password</p><span id="pw-error" class="error-text">This field is required</span>
							<input type="text" name="form[register][password1]" id="form[register][password1]" value="" onclick="return false;"/>
							<div class="clear"></div>
						</div>
						
						<div>
							<p class="password important">Terms</p><span id="terms-error" class="error-text">Please accept the Terms in order to register.</span>
							
							<p class="important"><input type="checkbox" name="form[register][terms]" id="form[register][terms]" class="checkbox"/> By requesting membership, I accept the Terms and Conditions of Totsy, and accept to receive sale email newsletters. Totsy will never sell or give my email to any outside party.</p>
							<div class="clear"></div>
						</div>
					</form>
				</div>	
				<div id="register-tools" class="container">
					<div>
						<br><input type="submit" value="Register" title="Register Now" alt="Register Now" class="register-button" id="registerButton" onclick="return false;"/>
					</div>
					
					<div id="register-load-indicator">
						<img src="http://www.totsy.com/img/motw/ajax-loader.gif">
					</div>
				</div>
				
				<div id="clownpants" class="container">
					<img src="http://dev.totsy.com/img/motw/alex/1_3.jpg" />
					<img src="http://dev.totsy.com/img/motw/alex/711W_1.jpg" />
					<img src="http://dev.totsy.com/img/motw/alex/712W-1-1.jpg" />
					<img src="http://dev.totsy.com/img/motw/alex/721X-1.jpg" />
				</div>				
						
						
				<div class="clear"></div>
			</div>
			
		</div>
										
	</div>


	
	<div class="clear"></div>
</div>
            </div>
        </fb:visible-to-connection>
   </div>
</div>