	<h2>How Totsy Works</h2> 
    <hr>
	<p>
		Totsy offers moms on-the-go and moms-to-be access to brand-specific sales, up to 90% off retail, just for them and the kids, ages 0-8.
	</p>

	<h2>Top brands for mom, baby, and child</h2>
	<hr />
	<p>
		Prenatal care products, baby gear, travel accessories, bedding and bath, children's
		clothing, toys, DVDs, and educational materials are just a sampling of a selection that promises only the best in quality and designer brands.
	</p>

	<h2>By invitation only</h2>
	<hr />
	<p>
		Membership is by invitation or request only. But the sooner you join, the better. Each sale
		lasts only 48 to 72 hours.
	</p>

	<h2>100% eco-friendly</h2>
	<hr />
	<p>
		Totsy is the first company in private sales to take on sustainable and socially responsible
		initiatives in all areas of business.
	</p>

	<h2>One baby, one tree</h2>
	<hr />
	<p>
		Totsy plants one tree in honor of your child when you make your first purchase. And, every time you shop with us - we’ll keep it watered for you!  Who says shopping can't save the world?
	</p>

<p></p>
<?php if (!empty($userInfo)){ ?>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
<?php } ?>