<?php if ($message){ echo $message; } ?>
<style>
h2 {
    color: #999999;
    font-size: 18px;
    font-weight: normal;
    margin: 0;
    padding: 0;
}
h2.tagline { margin:97px 0px 0px 0px; padding:10px 0px 10px 0px; color:#ed1c25; font-size:18px; font-weight: normal; }
.round { border-radius:12px; background: #fff; padding:14px; }
.pushy { margin-top:35px; padding:0px; }
.free_shipping_banner_reg_new img { position: absolute; right:-73px; top:454px; z-index:9999; }
.round_clear { border-radius:22px; -moz-border-radius:22px; -webkit-border-radius:22px; border: 10px solid rgba(255, 255, 255, 0.8); }
label { width:179px !important; }
#bug_bullets {
margin-left: 0;
padding-left: 0;
list-style: none;
}
#bug_bullets li {
background-image: url("/img/bug_bullets.png");
background-position: 0 13px;
background-repeat: no-repeat;
line-height: 32px;
padding: 4px 0 0 22px;
font-size:16px; color:#999999; font-weight:normal;
}

div.rollover_img {
width: 108px;
height: 108px;
background-image: url(/img/freeShip-badge.png);
position: absolute; right:-73px; top:406px;
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

.video { margin-top: 97px;] }

.loginformlabel{
	width:50px;
}

.inputbox{
	float:left;
}

.button{
	width:120px;
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
<div class="container_16 round_clear pushy" style="width:720px; height:473px; float:left; margin:85px 0px 0px 85px;">
<div class="round gradient"  style="background:#ffffff; height:445px;">

<!-- left side -->
<div class="grid_6" style="float:left;width:315px;">
<?php echo $this->html->link($this->html->image('logo_reg_new.png', array('width'=>'280')), '', array('escape'=> false)); ?>
<div class="round gradient" style="border:1px #eeeeee solid; height:330px; width:310px; margin-top:8px;">

<h2 style="width:300px; text-align:center; font-weight:bold; padding-top:10px; padding-bottom:20px; margin-bottom:10px;  border-bottom:1px #cccccc solid;">Member Sign in</h2>


<?php echo $this->view()->render(array('element' => 'loginForm')); ?>
</div>
<div class="clear"></div>
</div>



<!-- right side -->

<div class="grid_6" style="float:right; ">
<div class="fr" style="padding-right:20px;padding-top:10px;">Not a member yet? <a href="/register" title="Sign In">Join now!</a></div>
<div class="round gradient" style="border:1px #eeeeee solid; height:330px; width:300px; margin-top:83px;margin-left:20px;">
<img src="/img/logos_home_right.jpg">
</div>

<div class="free_shipping_banner_reg_new rollover_img" style="margin-top:0px;"><a href="javascript:;" title="Free Shipping"><span></span></a></div>
</div>




</div>
<div class="clear"></div>
<?php //echo $this->html->image('featured_brands_long.png', array('style' => 'margin-top:20px; margin-left:10px; border-top:1px solid #f1f1f1; margin-bottom: -14px;')); ?>
<div class="clear"></div>
</div>
<div id="footer">
<?php echo $this->view()->render(array('element' => 'footerNavPublic')); ?>
</div>
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
