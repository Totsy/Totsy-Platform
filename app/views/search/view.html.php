<?php $this->title("Page Not Found"); ?>

<h1>We're sorry <?="{$userInfo['firstname']}"; ?>, we couldn't find what you were looking for.</h1>

<hr />

<p>Please continue browsing <a href="/" title="Totsy's Sales">Today's Sales</a></p>
<br/><br/>

<!-- 
<h3>Other Products You May Enjoy</h3>
<hr/>
<div style="height:400px;">
	<div style="background:#f2f2f2; width:120px; height:120px; border:1px solid #ddd; display: block; float:left; margin:0px 10px 10px 0px;">
	</div>
	<div style="background:#f2f2f2; width:120px; height:120px; border:1px solid #ddd; display: block; float:left; margin:0px 10px 10px 0px;">
	</div>
	<div style="background:#f2f2f2; width:120px; height:120px; border:1px solid #ddd; display: block; float:left; margin:0px 10px 10px 0px;">
	</div>
	<div style="background:#f2f2f2; width:120px; height:120px; border:1px solid #ddd; display: block; float:left; margin:0px 10px 10px 0px;">
	</div>
	<div style="background:#f2f2f2; width:120px; height:120px; border:1px solid #ddd; display: block; float:left; margin:0px 10px 10px 0px;">
	</div>
	<div style="background:#f2f2f2; width:120px; height:120px; border:1px solid #ddd; display: block; float:left; margin:0px 10px 10px 0px;">
	</div>
	<div style="background:#f2f2f2; width:120px; height:120px; border:1px solid #ddd; display: block; float:left; margin:0px 0px 10px 0px;">
	</div>
-->
</div>

<div align="center"><img src="/img/error-img.jpg" alt="" /></div>

	<?php if (!count($events)) { ?>
        <?php return; ?>
    <?php } ?>
   