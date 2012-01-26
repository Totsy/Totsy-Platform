<script type="text/javascript">

$('#password-prompt a.btn-submit').live('click', function(e) {
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
		$('.ui-dialog').dialog('close');
		location.href = this.parentNode.action;
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
<div data-role="page" id="password-prompt">
	<div data-role="header"><h2>Enter your Password</h2></div>
	<div data-role="content">
<? if($user['requires_set_password']): ?>
		<h3>You've made it this far - let's create a Totsy account!</h3>
<? else: ?>
		<h3>You must enter your password to continue.</h3>
<? endif; ?>
<? if($user['requires_set_password']): ?>
		<p>Registration only takes a moment.<br/>By proceeding you agree to the <a href="/pages/terms">terms and conditions</a>.</p>
<? endif; ?>
		<form action="" method="post">
<? if($user['requires_set_password']): ?>
			<label for="email">Email Address:</label>
			<input type="text" id="email" name="email" value="<?= $user['email'] ?>" />
<? endif; ?>
			<label for="pwd">Password:</label>
			<input type="password" id="pwd" name="pwd" placeholder="Enter Password" />
			<a href="#" class="btn-submit" data-role="button"><?= $user['requires_set_password'] ? 'Create Account' : 'Continue' ?></a>
		</form>
	</div>
</div>