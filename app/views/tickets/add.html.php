<?php
	use app\models\Menu;

	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	echo $this->MenuList->build($menu, $options);
?>	
		<!-- Start Main Page Content -->
		<div id="middle" class="noright">				
			
			<div class="tl"></div>
			<div class="tr"></div>
			<div id="page">
			
				<h2 class="gray">My Support Tickets</h2>

				<!-- Replace with account welcome message -->
				<p>You have submitted no tickets</p>
				<?php if (!empty($message)): ?>
					<?php echo $message ?>
				<?php endif ?>
					<?=$this->form->create(); ?>
					<fieldset class="fl">
					
						<legend class="bold">Create new ticket</legend>
						
							
						
						<label for="title">Title</label><br />
						<input type="text" id="title" class="inputbox" name="title" style="width:300px" />
						
						<br />
						
						<label for="order">Assign order</label><br />
						<select name="order" id="order" style="width:310px" class="inputbox">
							<option value="">-- Select an order --</option>
						</select>
						
						<br />
						
						<label for="message">Message</label><br />
						<textarea name="message" id="message" class="inputbox" style="width:300px;height:120px"></textarea>
						
						<br />

					<?=$this->form->submit('Submit', array('class' => "flex-btn fr" )); ?>
					</fieldset>
					<?=$this->form->end(); ?>
			
			</div>
			<div class="bl"></div>
			<div class="br"></div>
			
		</div>
		
	</div>
	<!-- End Main Content -->
	
</div>


<script type="text/javascript" src="../js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="../js/jquery.equalheights.js"></script>

<!-- This equals the hight of all the boxes to the same height -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".r-box").equalHeights(100,300);
	});
</script>

</body>

</html>
