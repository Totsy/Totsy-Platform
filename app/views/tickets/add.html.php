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
				<p>Hello <?=$userInfo['firstname']?>, <br><br>We hope to quickly resolve any issue you may have with your order.<br> 
					Please use send us a message with as much detail as possible for us to assist you.</p>
			</div>
				<fieldset class="fl">
					<legend class="bold">Create new ticket</legend>
					<label for="title">Title</label><br />
					<input type="text" id="title" class="inputbox" name="title" style="width:300px" />
					<br />
					<label for="order">Assign order</label><br />
						<?=$this->form->select('order', $orders, array('style' => 'width:310px')); ?>
					<br />
					<label for="message">Message</label><br />
					<?=$this->form->textarea('message', array(
						'class' => 'inputbox',
						 'style' => 'width:300px;height:120px'
						)); 
					?>
					<br />
				<?=$this->form->submit('Submit', array('class' => "flex-btn fr" )); ?>
				</fieldset>
			<?=$this->form->end(); ?>
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
