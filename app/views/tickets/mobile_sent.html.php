<h2>Contact Us</h2>
<hr />
<p>Thank you for your message, we will be contacting you shortly.</p>
<p></p>
<?php echo $this->view()->render(array('element' => 'mobile_aboutUsNav')); ?>
<?php echo $this->view()->render(array('element' => 'mobile_helpNav')); ?>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>

