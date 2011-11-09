<style>
	h2 { margin:0px; padding:0px; color:#999; font-size:15px; }
	h2.tagline { margin:97px 0px 0px 0px; padding:10px 0px 10px 0px; color:#ed1c25; font-size:18px; font-weight: normal; }
	.round { border-radius:12px; background: #fff; padding:14px; }
	.pushy { margin-top:35px; padding:0px; }
	.free_shipping_banner_reg_new img { position: absolute; right:92px; top:439px; z-index:9999; }
	.round_clear { border-radius:22px; border: 10px solid rgba(255, 255, 255, 0.8); }
	label { width:149px !important; }
	li { font-size:14px; color:#808080; font-weight:normal;}
	
	#bug_bullets {
	margin-left: 0;
	padding-left: 0;
	list-style: none;
	}
	
	#bug_bullets li {
	background-image: url("/img/bug_bullets.png");
	background-position: 0 6px;
	background-repeat: no-repeat;
	line-height: 28px;
	padding: 0 0 0 18px;
}
</style>
<div class="container_16 round_clear pushy" style="width:771px;">
<div class="round">
	<!-- left side -->
	<div class="grid_6">
			<?php echo $this->html->link($this->html->image('logo_reg_new.png', array('width'=>'333')), '', array('escape'=> false)); ?>
			<div style="width:350px; margin-top:20px;">
				<?php echo $this->view()->render(array('element' => 'registrationForm')); ?>
			</div>
			<div class="clear"></div>
	</div>
	<!-- right side -->
	<div class="grid_6" style="margin-left:28px;">
			<div class="fr">Already a member? <a href="/login" title="Sign In">Sign In</a></div>
			<h2 class="tagline">Why savvy moms shop at Totsy?</h2>
			<ul id="bug_bullets">
					<li>Membership is free</li>	
					<li>Exclusive sales for kids, moms and families</li>	
					<li>Savings of up to 90% off retail</li>	
					<li>Sales last up to 3 days</li>	
					<li>A tree is planted for your first purchase</li>	
					<li>Refer friends and earn Totsy credits</li>	
			</ul>
			
	</div>
	
<div class="clear"></div>	
	<div class="free_shipping_banner_reg_new"><img src="/img/freeShip-badge.png" /></div>
	<?php echo $this->html->image('featured_on_long.png', array('style' => 'margin-top:20px; margin-left:10px;')); ?>
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
