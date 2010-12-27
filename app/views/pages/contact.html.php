<?php if (!empty($userInfo)): ?>
	<?=$this->menu->render('about'); ?>
<?php endif ?>


<h1 class="p-header"><?=$this->title("Contact Us"); ?></h1>

<?php if (!empty($userInfo)): ?>
	<div id="middle" class="noright">
<?php else: ?>
	<div id="middle" class="fullwidth">
<?php endif ?>

	
	<div class="tl"><!-- --></div> 
	<div class="tr"><!-- --></div> 

	<div id="page">
		<div id="message">
				<strong>Corporate Address:</strong><br/>
				10 West 18th Street<br/>
				4th Floor<br/>
				New York, NY 10011<br/>
				<br />
				<strong>Support Email:</strong> <br />				 
				<a href="mailto:support@totsy.com">support@totsy.com</a></p>
		</div>
	</div>
	<div class="bl"><!-- --></div>
	<div class="br"><!-- --></div>
</div>
