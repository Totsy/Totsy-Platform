<style>
	h2 { margin:0px; padding:0px; color:#999; font-size:15px; }
	h2.tagline { margin:0px; padding:10px 0px 10px 0px; color:#ed1c25; font-size:24px; font-weight: normal; }
	.round { border-radius:12px; background: #fff; padding:14px; }
	.pushy { margin-top:35px; padding:0px; }
	.free_shipping_banner_reg_new img { position: absolute; right:-50px; top:430px; z-index:9999; }
	.round_clear { border-radius:22px; border: 10px solid rgba(255, 255, 255, 0.8); }
</style>
<div class="container_16 round_clear pushy">
<div class="round">
	<div class="grid_10" style="width:560px;">
			<h2 class="tagline">Save up to 90% off the best brands for your family!</h2>
			<iframe width="540" height="315" src="http://www.youtube.com/embed/HJBQnkxPJko" frameborder="0" allowfullscreen></iframe>
			<?php echo $this->html->image('featured_on.png', array()); ?>
	</div>

	<div class="grid_6" style="padding:0px 0px 0px 2px;">
			<div>Already a member? <a href="/login" title="Sign In">Sign In</a></div>
			<div class="clear"></div>
			<div style="padding-top:25px;">
				<?php echo $this->html->link($this->html->image('logo_reg_new.png', array('width'=>'333')), '', array('escape'=> false)); ?>
			</div>
			<?php echo $this->view()->render(array('element' => 'registrationForm')); ?>
			<div class="free_shipping_banner_reg_new"><img src="/img/freeShip-badge.png" /></div>
	</div>
	<div class="clear"></div>
</div>
</div>

<div id="footer">
	<?php echo $this->view()->render(array('element' => 'footerNavPublic')); ?>
</div>
<!-- Google Code for Homepage Remarketing List -->
<script type="text/javascript">
/* <![CDATA[ */
	var google_conversion_id = 1019183989;
	var google_conversion_language = "en";
	var google_conversion_format = "3";
	var google_conversion_color = "666666";
	var google_conversion_label = "8xkfCIH8iwIQ9Yb-5QM";
	var google_conversion_value = 0;
/* ]]> */
</script>

<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1019183989/?label=8xkfCIH8iwIQ9Yb-5QM&amp;guid=ON&amp;script=0"/>
	</div>
</noscript>
<!-- END OF Google Code for Homepage Remarketing List -->

<script>
	//your fb login function
	function fblogin() {
	FB.login(function(response) {
		}, {perms:'publish_stream,email,user_about_me,user_activities,user_birthday,user_groups,user_interests,user_location'});
	}
</script>