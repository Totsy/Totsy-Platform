<?php $this->title("Daily Deals"); ?>
<style>
	h3 {font-size:14px; color:#707070; font-weight: bold; margin-bottom: 15px;}
	h3.price {font-size:18px; color:#707070; font-weight: bold; margin-bottom: 15px;}
	p {font-size: 12px; font-weight: normal; color:#707070; margin-bottom: 20px; line-height: 16px; }
	hr {margin:10px auto;}
</style>
<div class="grid_16">
	<h2 class="page-title gray"><span class="red"><a href="/dailydeals" title="Sales">Daily Deals</a> /</span> <a href="/sale/" title="Sea World">Sea World</a></h2>
	<hr />
</div>

<div class="grid_11">
	<?=$this->html->image('/img/dailydeal_seaworld.png', array('style' => 'width:650px')); ?>
	<h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h3>
	<p>Maecenas sodales lacus non mi pellentesque rhoncus. Cras arcu lacus, tempor eu suscipit eu, vehicula sit amet turpis. Morbi consectetur interdum auctor. Duis facilisis diam non metus semper sit amet laoreet enim sollicitudin. In bibendum, lectus sit amet vestibulum faucibus, metus nulla gravida odio, id pretium mauris lacus ac justo. Nam feugiat, augue vel tempor rhoncus, augue ligula imperdiet purus, quis bibendum sapien </p>
	<h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h3>
	<p>Maecenas sodales lacus non mi pellentesque rhoncus. Cras arcu lacus, tempor eu suscipit eu, vehicula sit amet turpis. Morbi consectetur interdum auctor. Duis facilisis diam non metus semper sit amet laoreet enim sollicitudin. In bibendum, lectus sit amet vestibulum faucibus, metus nulla gravida odio, id pretium mauris lacus ac justo. Nam feugiat, augue vel tempor rhoncus, augue ligula imperdiet purus, quis bibendum sapien </p>
	<h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h3>
	<p>Maecenas sodales lacus non mi pellentesque rhoncus. Cras arcu lacus, tempor eu suscipit eu, vehicula sit amet turpis. Morbi consectetur interdum auctor. Duis facilisis diam non metus semper sit amet laoreet enim sollicitudin. In bibendum, lectus sit amet vestibulum faucibus, metus nulla gravida odio, id pretium mauris lacus ac justo. Nam feugiat, augue vel tempor rhoncus, augue ligula imperdiet purus, quis bibendum sapien </p>
	<h3>FINAL SALE</h3>
</div>
	<div class="grid_5">
		<div class="md-gray roundy" style="padding:20px; margin:0px 0px 10px 0px;">
			<h3 class="caps price" style="text-align:center;">$50 Totsy Price</h3>
			<input type="button" value="BUY NOW" id="add-to-cart" class="button" style="margin-left:85px">
			<hr />
			<?=$this->html->image('/img/dailydeal_percentage_box.png', array('style' => 'margin-left:8px;')); ?>
			<span style="text-align:center;position:absolute; top:125px; right:211px;">Value<br/><strong>$100</strong></span>
			<span style="text-align:center;position:absolute; top:125px; right:123px;">Discount<br/><strong>50%</strong></span>
			<span style="text-align:center;position:absolute; top:125px; right:47px;">Saving<br/><strong>$50</strong></span>
			<p class="red" style="font-size:14px; font-weight:bold; text-align:center;">Closes in 4 Days, 07:15:03</p>
			<hr />
			<div style="text-align:center;">Share The Savings with your Friends</div>
			<?=$this->html->image('/img/dailydeal_num_purchased.png', array('style' => 'margin-left:7px;')); ?>
			<span style="position:absolute; top:264px; right:90px;"><strong>100</strong> deals purchased</span>
			<div>
				<?php echo $this->html->link($this->html->image('/img/dailydeal_social_icons_02.png', array('style' => 'float:left; margin-left:20px;')), '#', array('escape'=> false)); ?>
				<?php echo $this->html->link($this->html->image('/img/dailydeal_social_icons_03.png', array('style' => 'float:left;')), '#', array('escape'=> false)); ?>
				<?php echo $this->html->link($this->html->image('/img/dailydeal_social_icons_04.png', array('style' => 'float:left;')), '#', array('escape'=> false)); ?>
				<?php echo $this->html->link($this->html->image('/img/dailydeal_social_icons_05.png', array('style' => 'float:left;')), '#', array('escape'=> false)); ?>
				<?php echo $this->html->link($this->html->image('/img/dailydeal_social_icons_06.png', array('style' => 'float:left;')), '#', array('escape'=> false)); ?>
				<?php echo $this->html->link($this->html->image('/img/dailydeal_social_icons_07.png', array('style' => 'float:left;')), '#', array('escape'=> false)); ?>
			</div>
			<div class="clear"></div>
			<hr />
			<h3>Need to Know</h3>
			<p>Maecenas sodales lacus non mi pellentesque rhoncus. Cras arcu lacus, tempor eu suscipit eu, vehicula sit amet turpis. Morbi consectetur interdum auctor. Duis facilisis diam non metus semper sit amet laoreet enim sollicitudin. In bibendum, lectus sit amet vestibulum faucibus, metus nulla gravida odio, id pretium mauris lacus ac justo. Nam feugiat, augue vel tempor rhoncus, augue ligula imperdiet purus, quis bibendum sapien </p>
			<hr />
			<h3>Location</h3>
			<p>Everywhere! <a href="#" target="_blank" title="Website">Website</a></p>
			<hr />
			<h3>Questions About this Deal?</h3>
			<p><a href="mailto:support@totsy.com">support@totsy.com</a></p>
		</div>
</div>


<script type="text/javascript">
$(function () {
	var saleEnd = new Date();
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	$('#listingCountdown').countdown({until: saleEnd, layout: 'Ends in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
});
</script>