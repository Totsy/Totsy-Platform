<?php $this->title("Being Green"); ?>

<h2>Being Green</h2>
<hr />
	<h3 class="page-title gray">TOTSY IS THE FIRST PRIVATE SALES COMPANY TO GO GREEN</h3>
	<p>For today's world and tomorrow's generation, Totsy is the first company in private sales to take on sustainable and socially responsible initiatives in all areas of business. It’s not always easy being green, but it’s worth it for the future of our children! 
	</p>
	<br />
	<p><a href="http://www.terrapass.com/partners/totsy/?utm_source=totsy&utm_campaign=smb-partner" target="_blank">
			<?=$this->html->image('being_green/terrapass.gif', array(
				'align' => 'left',
				'alt' => 'Totsy - carbon balanced with TerraPass',
				'style' => "border: none; margin-right: 15px; margin-bottom:10px;"
			)); ?>
		</a>
	</p>
		<h3 class="page-title gray"> HOW TOTSY IS GREEN </h3>
		<p>Totsy is environmentally conscious in everything we do. We recycle at the office, use less packaging materials when shipping, and support eco-friendly partners whenever possible.</p>
		<p>And don't forget about our carbon footprint! Through a partnership with TerraPass, Totsy has reduced its carbon emissions by sponsoring clean energy and carbon reduction projects. Held up to the highest standards in environmental leadership, the conservation and efficiency measures we implement now will help us continue to lower our carbon footprint.</p>
	<br />
	<h3 class="page-title gray">One Baby, One Tree&trade;</h3>
	<hr />
		<?=$this->html->image('being_green/carbonzero.gif', array(
			'align' => 'left', 'style' => 'margin-right: 15px; margin-bottom:10px;'
		)); ?>
		<h3 class="page-title gray"> YOUR PURCHASE MAKES A DIFFERENCE </h3>
		<p>Totsy plants one tree in honor of your child when you make your first purchase. And, every time you shop with us - we’ll keep it watered for you! As your tree grows bigger, it continues to do more for the environment! Together with Pure Planet and Objective Carbon Zero, we are reducing the effects of deforestation, slowing the effects of global warming, preserving biodiversity, and helping small-scale farmers continue to produce diverse and localized crops.</p>
		<br />
		<h3 class="page-title gray"> WHERE TREES ARE PLANTED </h3>
		<p>Your trees will be planted in Alto Huayabamba, located in the Amazonian highlands in Tarapoto, Peru. This is the most established reforestation project developed by Pure Planet.</p>

<p></p>
<?php echo $this->view()->render(array('element' => 'mobile_aboutUsNav')); ?>
<?php echo $this->view()->render(array('element' => 'mobile_helpNav')); ?>
<?php if (!empty($userInfo)){ ?>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
<?php } ?>


