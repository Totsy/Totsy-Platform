<?php $this->title("My Account Credits"); ?>
<h1 class="p-header">My Account</h1>
<?=$this->menu->render('left'); ?>

<div id="middle" class="noright">
<div class="tl"></div>
<div class="tr"></div>
<div id="page">
	
	<h2 class="gray mar-b">Credits</h2>

	<p>We're in the process of migrating all customer credit history to the new platform. Rest assured that if you have earned credits in the past, your credits are not lost.</p>

	<p>Your credits will be here within the next few days, and new credits are already functional so you won't miss out on any opportunities while we are migrating the data from our old site.</p>

	<p>Please check back and <?=$this->html->link('contact us', array('Tickets::add')); ?> if, after a few days you feel your credits failed to make the move to our shiny new home.</p>

</div>
<div class="bl"></div>
<div class="br"></div>