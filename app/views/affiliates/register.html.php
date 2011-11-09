<style>
h2 { margin:0px; padding:0px; color:#999; font-size:15px; }
h2.tagline { margin:97px 0px 0px 0px; padding:10px 0px 10px 0px; color:#ed1c25; font-size:18px; font-weight: normal; }
.round { border-radius:12px; background: #fff; padding:14px; }
.pushy { margin-top:35px; padding:0px; }
.free_shipping_banner_reg_new img { position: absolute; right:-73px; top:387px; z-index:9999; }
.round_clear { border-radius:22px; border: 10px solid rgba(255, 255, 255, 0.8); }
label { width:149px !important; }
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
font-size:14px; color:#808080; font-weight:normal;
}

.rollover_img {
width: 108px;
height: 108px;
background-image: url(/img/freeShip-badge.png);
position: absolute; right:-82px; top:400px;
}

.rollover_img a {
width: 108px;
height: 108px;
display: block;
text-decoration: none;

}

.rollover_img a:hover {
width: 108px;
height: 108px;
background-image: url(/img/freeShip-badge_hover.png);
}

.rollover_img a span {
display: none;
width: 108px;
}

.rollover_img a:hover span {
display: block;
}
.gradient {background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top, #ffffff 0%, #f5f5f5 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#f5f5f5)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top, #ffffff 0%,#f5f5f5 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top, #ffffff 0%,#f5f5f5 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top, #ffffff 0%,#f5f5f5 100%); /* IE10+ */
background: linear-gradient(top, #ffffff 0%,#f5f5f5 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f5f5f5',GradientType=0 ); /* IE6-9 */
}
</style>
<div class="container_16 round_clear pushy" style="width:771px;">
<div class="round gradient">
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
<div class="free_shipping_banner_reg_new rollover_img"><a href="javascript:;" title="Free Shipping"><span></span></a></div>
</div>
<div class="clear"></div>
<?php echo $this->html->image('featured_on_long.png', array('style' => 'margin-top:20px; margin-left:10px; border-top:1px solid #f1f1f1;')); ?>
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