<?php $this->title("My Account Credits"); ?>
<h1 class="p-header">My Account</h1>
<?=$this->menu->render('left'); ?>

<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		
		<h2 class="gray mar-b">My Credits</h2>
		<hr />
		<p>Credits from the old Totsy website have been moved over, and you should see that amount available at the top of the page next to the text "My Credits".</p>
		
		<p>We've taken great pains to restore your credits from the old Totsy website; if you think your credits did not make it over from your original Totsy account, please <?=$this->html->link('contact us', array('Tickets::add')); ?> and we can verify the status of your account.</p>
	
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
