<?php use lithium\storage\Session; ?>
<?php $this->title("About Us"); ?>
<div class="grid_16">
	<h2 class="page-title gray">About Us</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'aboutUsNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>


<div class="grid_11 omega roundy grey_inside b_side">
	
	<?php if(Session::read("layout", array("name"=>"default"))=="mamapedia"): ?>
	<p>
		Mamasource has partnered with Totsy to provide all Mamasource members with the best deals on top brands for babies, kids and moms.
	</p>

	<br />	
	<?php endif ?>

	<h2 class="page-title gray">Where the savvy mom shops</h2>
	<hr />
	<p>
		Totsy offers moms on-the-go and moms-to-be access to brand-specific sales, up to 90% off
		retail, just for them and the kids, ages 0-8.
	</p>

	<br />
	
	<h2 class="page-title gray">Top brands for mom, baby, and child</h2>
	<hr />
	<p>
		Prenatal care products, baby gear, travel accessories, bedding and bath, children's
		clothing, toys, DVDs, and educational materials are just a sampling of a selection that
		promises only the best in quality and designer brands.
	</p>

	<br />
	<h2 class="page-title gray">By invitation only</h2>
	<hr />
	<p>
		Membership is by invitation or request only. But the sooner you join, the better. Each sale
		lasts only 48 to 72 hours.
	</p>

	<br />
	<h2 class="page-title gray">100% eco-friendly</h2>
	<hr />
	<p>
		Totsy is the first company in private sales to take on sustainable and socially responsible
		initiatives in all areas of business.
	</p>

	<br />
	<h2 class="page-title gray">One baby, one tree</h2>
	<hr />
	<p>
		Totsy plants one tree in honor of your child when you make your first purchase. And, every time you shop with us - we’ll keep it watered for you!  Who says shopping can't save the world?
	</p>
	<br />

</div>
</div>
<div class="clear"></div>
