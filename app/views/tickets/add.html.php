<?php $this->title("My Help Desk"); ?>
<h1 class="p-header">My Account</h1>
<?=$this->menu->render('left'); ?>

<!-- Start Main Page Content -->
<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<h2 class="gray">My Support Tickets</h2>
		<br>
		<?php if (!empty($message)): ?>
			<div class="standard-message"><?=$message;?></div>
		<?php endif ?>
			<?=$this->form->create(); ?>
			<div id="message">
				<p>Hello <?=$userInfo['firstname']?>, <br><br>We hope to quickly resolve any issue you may have.</p> 
				<p>Please <a href="mailto:support@totsy.com" title="click to send us an email at support@totsy.com">send us a message</a> with as much detail as possible for us to assist you.</p>
				<p>You can also contact Totsy at:<br>
					Corporate Address: 27 West 20th Street, Suite 400, New York, NY 10011<br>
					Phone Number: 1-888-59TOTSY (1-888-598-6879) </p>
			</div>
	</div>
	
	<div class="bl"></div>
	<div class="br"></div>
</div>
<script type="text/javascript" src="../js/jquery.equalheights.js"></script>
<!-- This equals the hight of all the boxes to the same height -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".r-box").equalHeights(100,300);
	});
</script>
