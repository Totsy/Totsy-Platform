<?php $this->title("My Credits"); ?>

<div class="grid_16">
	<h2 class="page-title gray">My Credits</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'aboutUsNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>

<div class="grid_11 omega roundy grey_inside b_side">

	<h2 class="page-title gray">My Credits</h2>
	<hr />
	<p>Credits from the old Totsy website have been moved over, and you should see that amount available at the top of the page next to the text "My Credits".</p>
		
		<p>We've taken great pains to restore your credits from the old Totsy website; if you think your credits did not make it over from your original Totsy account, please <?php echo $this->html->link('contact us', array('Tickets::add')); ?> and we can verify the status of your account.</p>
	<br />

</div>
</div>
<div class="clear"></div>
