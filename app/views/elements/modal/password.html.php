<script type="text/javascript">

$('#password-prompt form').live('submit', function(e) {
	e.preventDefault();
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
		location.href = this.action;
	} else if(error) {
		alert(error);
		$('#pwd').focus();
	} else {
		alert('The password entered is incorrect. Please re-type your password and try again.');
		$('#pwd').focus();
	}

	return success;
});

</script>
<style type="text/css">

#password-prompt {
	text-align: center;
}

#password-prompt h2 {
	font-size: 24px;
	font-weight: normal;
	color: #333;
	margin-bottom: 16px;
}

#password-prompt p {
	font-size: 12px;
	margin-bottom: 20px;
}

#password-prompt label {
	float: left;
	clear: left;
	width: 250px;
	margin: 4px 8px 20px 0;
	font-size: 14px;
	text-align: right;
	color: #999;
}

#password-prompt input[type=text], 
#password-prompt input[type=password] {
	width: 200px;
	float: left;
	font-size: 16px;
	color: #666;
}

#password-prompt input[type=submit] {
	clear: left;
	display: block;
	margin-left: 250px;
}

</style>
<div id="password-prompt" class="no-show">
<? if($user['requires_set_password']): ?>
	<h2>Let's set a password for your Totsy account!</h2>
<? else: ?>
	<h2>Please confirm your Totsy password to purchase:</h2>
<? endif; ?>
	<form action="" method="post">
<? if($user['requires_set_password']): ?>
		<label for="email">Your Email Address:</label>
		<input type="text" id="email" name="email" value="<?= $user['email'] ?>" />
<? endif; ?>
		<label for="pwd"><? if($user['requires_set_password']) echo 'Create Your ' ?>Password:</label>
		<input type="password" id="pwd" name="pwd" placeholder="Enter Password" />
		<input type="submit" value="<?= $user['requires_set_password'] ? 'Create Account' : 'Continue' ?>" class="button" />
	</form>
</div>