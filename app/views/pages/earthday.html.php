
<style>


.earthday{
	width:736px; 
	margin-left:12px;
	height:772px; 
	background:url('/img/flowery-bg-image.jpg') no-repeat; 
	text-align:center;
	color:#767c70;
	padding-top:80px;
	padding-left:100px;
	padding-right:100px;
	font-size:15px;
	line-height:20px;
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
	font-style:italic;
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
	width:360px;
	height:80px;
	margin-bottom:20px;
}

.inputdiv{
	width:360px;
	height:20px;
}



</style>


<div class="earthday">
<?php if($_POST['earthdaybtn']){ ?>

<h1>CELEBRATE EARTH DAY SUBMITTED</h1>
<p>Thank you for entering!</p>

<?php }else{ ?>



<script>

function earthdayform(){
	alert('submitted');


}


</script>



<h1>CELEBRATE EARTH DAY</h1>
<H3>(and win prizes)</H3>

<p style="margin-top:20px;">At Totsy, we work hard to maintain eco-responsibility in our business practices. We recycle, reduce our packaging, take mass transit, plant trees and offset our carbon usage, just to name a few. We've learned that a million tiny things - really can add up to a whole lot. In fact they can change the world! And we know our customers care deeply about the world their children will inherit. So we'd like to hear from you too!</p>

<br><br>

<img src="/img/earthday_leaf.png">

<br><br>


<form method="post">
<h5>GIVEAWAY QUESTION</h5>
<h2>What little things do you do with your little ones,<br> to help make their world a greener place?</h2>


<h5>ANSWER HERE</h5>
<textarea cols=60 rows=20 name="comment" id="comment"></textarea>

<h5>ENTER EMAIL</h5>
<input type="text" name="email" id="email" class="inputdiv">

<br><br>
<img src="/img/earthday_enternow.png" onclick="javascript:earthdayform()">

<!-- 
<input type="submit" name="earthdaybtn" id="earthdaybtn" value="Enter Now">
 -->
</form>


<?php
}

?>



</div>


<p style="margin:15px;"><b>Prize:</b> Three lucky winners chosen at random will receive an eco-friendly prize pack from our generous friends. Plus, Totsy will donate $100 to our grand-prize winner's favorite environmental non-profit organization. At the end of the giveaway, please check back to our blog. We'll compile and share our favorite "Top Five Little Things We Can Do", to keep our planet green for the little ones we love most. </p>

<p style="padding-left:35px;">
<a href="#"><img src="/img/earthday_logo1.png" border="0"></a>
<a href="#"><img src="/img/earthday_logo2.png" border="0"></a>
<a href="#"><img src="/img/earthday_logo3.png" border="0"></a>
<a href="#"><img src="/img/earthday_logo4.png" border="0"></a>



</p>

