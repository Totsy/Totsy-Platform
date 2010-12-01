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
			Please <a href="mailto:support@totsy.com" title="Click to send us an email at support@totsy.com">click to email us</a> and we will do our best to respond quickly.</p>
			<p>You can also contact Totsy at:<br>
				Corporate Address: 10 West 18th Street, 4th Floor, New York, NY 10011<br>
				Phone Number: 1-888-59TOTSY (1-888-598-6879) </p>
		</div>
	</div>
	<div class="bl"><!-- --></div>
	<div class="br"><!-- --></div>
</div>
