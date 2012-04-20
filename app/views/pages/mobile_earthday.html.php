<style>
.earthday{
	text-align:left;
	color:#767c70;
	font-size:15px;
}

.earthday h1{
	color:#1c4501;
	font-size:40px;
	font-weight:normal;
}

.earthday h2{
	color:#1c4501;
	font-size:24px;
	padding-bottom:20px;
}

.earthday h3{
	color:#1c4501;
	font-size:24px;
	padding:0px;
	margin:0px 0px 5px 0px;
	font-weight:normal;
}

.earthday h4{
	color:#1c4501;
}

.earthday h5{
	color:#767c70;
	font-weight:bold;
	font-size:11px;
	margin-bottom:5px;
}

.earthday textarea{
	height:80px;
	margin-bottom:20px;
}

.inputdiv{
	height:20px;
}
.logos { background-color: #fff; display: block;}
.logos img { float:left; width:142px; padding:1px; }
</style>

<div class="earthday">
<?php if($_POST['email']) { ?>

<h3>SUBMITTED</h3>
<hr />
<p>Thank you for entering! <a href="/sales">Return to Totsy.com</a></p>

<?php } else { ?>

<script>

function earthdayform(){
	alert('submitted');
	document.getElementById("earthdayform").submit();

}


</script>

<h3>CELEBRATE EARTH DAY</h3>
<hr />
<p>(and win prizes)</p>

<p>At Totsy, we work hard to maintain eco-responsibility in our business practices. We recycle, reduce our packaging, take mass transit, plant trees and offset our carbon usage, just to name a few. We've learned that a million tiny things - really can add up to a whole lot. In fact they can change the world! And we know our customers care deeply about the world their children will inherit. So we'd like to hear from you too!</p>

<br />

<img src="/img/earthday_leaf.png">

<br /><br />

<form method="post" id="earthhdayform">
	<h3>GIVEAWAY QUESTION</h3>
	<hr/>
	<p>What little things do you do with your little ones to help make their world a greener place?</p>

	<h5>ANSWER HERE</h5>
	<hr />
	<textarea cols="60" rows="20" name="comment" id="comment"></textarea>

	<h5>ENTER EMAIL</h5>
	<hr />
	<input type="text" name="email" id="email" class="inputdiv">
	<br />
	<img src="/img/earthday_enternow.png" onclick="javascript:earthdayform()">
	<!-- 
	<input type="submit" name="earthdaybtn" id="earthdaybtn" value="Enter Now">
 	-->
</form>

</div>

<p style="margin:15px;"><strong>Prize:</strong> Three lucky winners chosen at random will receive an eco-friendly prize pack from our generous friends. Plus, Totsy will donate $100 to our grand-prize winner's favorite environmental non-profit organization. At the end of the giveaway, please check back to our blog. We'll compile and share our favorite "Top Five Little Things We Can Do", to keep our planet green for the little ones we love most. No purchase necessary. Sweepstakes starts on 4/20/12 and ends on 4/30/12 at 11:59 pm ET.</p>

<hr />

<div class="logos">
	<a href="http://wearyourintention.com/" target="_blank"><img src="/img/earthday_logo1.png" border="0"></a>
	<a href="http://bumkins.com/" target="_blank"><img src="/img/earthday_logo2.png" border="0"></a>
	<a href="http://fullcirclehome.com/" target="_blank"><img src="/img/earthday_logo3.png" border="0"></a>
	<a href="http://iplaybabywear.com/" target="_blank"><img src="/img/earthday_logo4.png" border="0"></a>
<div class="clear"></div>
</div>
<?php } ?>