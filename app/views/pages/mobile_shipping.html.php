<h2>Shipping &amp; Tax</h2>
	<hr />
	<p>For delivery, orders will be processed, ordered to the brand, received at Totsy, quality controlled, and then shipped, and delivered within 3-5 weeks after the sale close. Your items are ordered to the brand after you purchase them so some brands might be a bit slow to ship those items to us. Make sure you always consider this when choosing the size when ordering an apparel item. When your order is shipped, you will be able to access a tracking number on your personal account page.</p>

	<br />
	<h2 class="page-title gray">Sales Tax</h2>
	<hr />
	<p>Sales tax will be charged for orders in Delaware, Pennsylvania, and New York. You will be able to view the sales tax for your order, if applicable, before confirming payment.</p>

	<br />
	<h2 class="page-title gray">Shipping Rates</h2>
	<hr />
	<p>Shipping rates vary depending on the shipping preferences chosen (standard or express) and the location where the order will be shipped.</p>

	<br />
	<h2 class="page-title gray">Shipping Carrier</h2>
	<hr />
	<p>Standard delivery orders are shipped via USPS Priority Mail. Express delivery orders are shipped via UPS.</p>

<p></p>
<?php if (!empty($userInfo)){ ?>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
<?php } ?>
