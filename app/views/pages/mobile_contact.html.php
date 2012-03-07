<h2>Contact Us</h2>
	<hr />
	<p>
	Note: We are experiencing an issue with our email server that is causing a delay in email response. For faster service, please call us toll free at 888-247-9444. Totsy Customer Service is available Monday - Friday from 9:00 a.m. to 7:00 p.m. EST.</p>
	<p>
	<strong>Corporate Address:</strong><br/>
				10 West 18th Street<br/>
				4th Floor<br/>
				New York, NY 10011<br/>
				<br />

				<h3 class="gray">Contact Support</h3>
				<a href="mailto:support@totsy.com">support@totsy.com</a><br />
				888-247-9444<br />
				Office Hours:<br/> M-F 10am - 5pm EST</p>

<p></p>
<?php if (!empty($userInfo)){ ?>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
<?php } ?>
