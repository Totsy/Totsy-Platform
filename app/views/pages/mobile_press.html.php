<h2>Press</h2>
	<hr />
	<p>Totsy is the leading website in private sales for moms. We are also the first company of its kind to be <?=$this->html->link('100% green', array('Pages::being_green')); ?>. More than just exclusive savings, we are a hub of information, expert advice, and quality products perfect for mom, baby, and child.</p>
	<p>For media inquiries, please contact us at <a href="mailto:press@totsy.com">press@totsy.com</a>.</p>

<p></p>
<?php if (!empty($userInfo)){ ?>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
<?php } ?>

