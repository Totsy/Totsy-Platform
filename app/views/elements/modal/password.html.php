<script type="text/javascript">

$('#password-prompt .btn.continue').live('click', function(e) {
	var success = false, error = '';

	$.ajax({
		url: '/users/passwordVerify',
		type: 'POST',
		async: false,
		data: {
			'email': $('#email').val(),
			'pwd': $('#pwd').val()
		},
		dataType: 'json',
		success: function(data,status) {
			if ('success' == status) {
				success = data.result;
				if (data.errors.length > 0) {
					error = data.errors[0];
				}
			}
		}
	});

	if (success && !error) {
		return true;
	} else if(error) {
		alert(error);
		e.preventDefault();
		$('#pwd').focus();
	} else {
		alert('The password entered is incorrect. Please re-type your password and try again.');
		e.preventDefault();
		$('#pwd').focus();
	}

	return success;
});

</script>
<style type="text/css">

#password-prompt {
	padding: 20px;
}
#password-prompt h2 {
	font-size: 20px;
	font-weight: bold;
	color: #777;
	margin: 8px auto 12px;
	padding-bottom: 8px;
	border-bottom: dotted 1px #999;
}

#password-prompt label {
	width: 100%;
	display: block;
	margin-top: 16px;
	font-size: 14px;
	color: #999;
}

#password-prompt input[type=text], 
#password-prompt input[type=password] {
	width: 225px;
	color: #666;
	border: solid 1px #999;
	background: #fff;
	-webkit-box-shadow: inset 1px 1px 5px 0px #999999;
	   -moz-box-shadow: inset 1px 1px 5px 0px #999999;
	        box-shadow: inset 1px 1px 5px 0px #999999;
}

#password-prompt .btn.continue {
	padding: 3px 10px;
	margin-left: 20px;
	background-color: rgba(218, 80, 77, 1);
	color: #fff;
	text-decoration: none;
}

#password-prompt .btn.continue:hover {
	background-color:rgba(230,87,84,1);
}

#password-prompt > a[href='/reset'] {
	display: block;
	width: 100%;
	margin-bottom: 16px;
}

#password-prompt > strong {
	font-size: 14px;
	font-weight: bold;
}

</style>
<div id="password-prompt" class="modal no-show">
<? if($user['requires_set_password']): ?>
	<h2>Let's set a password for your Totsy account!</h2>
<? else: ?>
	<h2>To continue with your purchase please re-enter your password.</h2>
<? endif; ?>
<? if($user['requires_set_password']): ?>
	<label for="email">Your Email Address:</label>
	<input type="text" id="email" name="email" value="<?= $user['email'] ?>" />
<? endif; ?>
	<label for="pwd"><? if($user['requires_set_password']) echo 'Create Your ' ?>Password</label>
	<input type="password" id="pwd" name="pwd" placeholder="Enter Password" />
	<a href="#" class="btn continue"><strong>Continue</strong></a>
<? if(!$user['requires_set_password']): ?>
	<a href="/reset">Forgot your password? Click here to reset it.</a>
	<strong>Why are asking for your password again?</strong>
	<p class="hint">At Totsy, we're as concerned with your security as we are in finding you a great deal. That's why we want to make sure it's you before continuing with your purchase.</p>
<? endif ?>
</div>