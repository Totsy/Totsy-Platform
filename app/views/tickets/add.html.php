<?php
	use app\models\Menu;
	$this->title("My Help Desk");
	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	echo $this->MenuList->build($menu, $options);
?>	
		<!-- Start Main Page Content -->
<div id="middle" class="noright">				
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<h2 class="gray">My Support Tickets</h2>
		<br>
		<?php if (!empty($message)): ?>
			<?php echo $message ?>
		<?php endif ?>
			<?=$this->form->create(); ?>
			<div id="message">
				<p>Hello <?=$userInfo['firstname']?>, there will be a message here inviting you to submit a ticket</p>
			</div>
				<fieldset class="fl">
					<legend class="bold">Create new ticket</legend>
					<label for="title">Title</label><br />
					<input type="text" id="title" class="inputbox" name="title" style="width:300px" />
					<br />
					<label for="order">Assign order</label><br />
						<?=$this->form->select('order', $orders, array('style'=>'width:310px')); ?>
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

