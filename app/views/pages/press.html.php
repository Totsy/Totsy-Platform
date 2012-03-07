<?php $this->title("Press"); ?>

<div class="grid_16">
	<h2 class="page-title gray">About Us</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'aboutUsNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>

<div class="grid_11 omega roundy grey_inside b_side">

	<h2 class="page-title gray">Press</h2>
	<hr />
	<p>Totsy is the leading website in private sales for moms. We are also the first company of its
      kind to be
      <?php echo $this->html->link('100% green', array('Pages::being_green')); ?>
      . More than
      just exclusive savings, we are a hub of information, expert advice, and quality products
      perfect for mom, baby, and child.</p>
    <p>For media inquiries, please contact us at <a href="mailto:press@totsy.com">press@totsy.com</a>.</p>
    	<br />

</div>
</div>
<div class="clear"></div>
