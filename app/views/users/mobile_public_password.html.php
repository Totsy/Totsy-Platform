<?php 

use lithium\storage\Session; 
use app\controllers\UsersController;

$token = "";
$status = "";

if($this->_request->query['t']) {
	$token = $this->_request->query['t'];
}

if($this->_request->query['s']) {
	$status = $this->_request->query['s'];
}

?>
<h2>Change Your Password</h2>
	<hr />
			<div>
				<?php
					switch ($status) {
						case 'true' :
							echo "<div class=\"standard-message\">Your information has been updated.</div>";
							break;
						case 'false' :
							echo "<div class=\"standard-error-message\">Your current password is incorrect. Please try again.</div>";
							break;
						case 'errornewpass' :
							echo "<div class=\"standard-error-message\">Please check that your passwords match and try again.</div>";
							break;
						case 'shortpass' :
							echo "<div class=\"standard-error-message\">Password must be at least 6 characters.</div>";
							break;
					}
				?>
			</div>
			<?php echo $this->form->create(null, array('class' => "fl", 'url'=>'/publicpassword')); ?>
			<div>
				<input type="hidden" name="clear_token" value="<?php echo $token; ?>">
					<?php echo $this->form->label('new_password', 'New Password',array('class' => 'account' )); ?>
					<?php echo $this->form->password('new_password', array(
							'class' => 'required',
							'style' => 'display:inline-block;'
						))
					;?>
			</div>
			<div>
					<?php echo $this->form->label('password_confirm', 'Confirm Password',array('class' => 'account' )); ?>
					<?php echo $this->form->password('password_confirm', array(
							'class' => 'inputbox',
							'style' => 'display:inline-block;'
						))
					;?>
				</div>
				<br />
			<?php echo $this->form->submit('Change Password', array('class' => 'button fr')); ?>
			<?php echo $this->form->end();?>
			</div>
