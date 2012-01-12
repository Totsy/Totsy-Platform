<?php echo $this->html->script(array('jquery.nivo.slider.pack'));?>
<script src="/js/jquery.tmpl.js" type="text/javascript"></script>

<?php echo $this->html->script(array('cart-timer.js?v=007', 'cart-items-timer.js?v=007', 'cart-popup.js?v=007'));?>

<!-- template used for items on cart. jquery.tmpl.js driven -->
<?php echo $this->view()->render( array('element' => 'popupCartItems') ); ?>

<script>
(function($) {
	$.fn.rotate = function() {
		var container = $(this);
		var totale = container.find("div").size();
		var current = 0;
		var i = setInterval(function() {
			if (current >= totale) current = 0;
			container.find("div").filter(":eq("+current+")").fadeIn("slow").end().not(":eq("+current+")").fadeOut("slow");
			current++;
		}, 5000);
		return container;
	};
})(jQuery);
</script>
	<h2 class="page-title gray"><span class="_red">Today's Sales</span> </h2>
	<hr />
		<!--Disney -->
<!--	<div class="disney disney_splash">
		<p><strong>SPECIAL BONUS!</strong> Included with your purchase of $45 or more is a one-year subscription to <img src="/img/parents.png" align="absmiddle" width="95px" /> ( a $10 value ) <span id="disney">Offer &amp; Refund Details</span></p>
	</div>
-->
<div class="fullwidth">
	<?php $x = 0; ?>
	<?php $y = 0; ?>
	<?php foreach ($openEvents as $event): ?>
		<!-- Start product item -->
		<?php if ($y == 0): ?>
			<div class="grid_6">
		<?php endif ?>
		<?php if ($y == 1): ?>
			<div class="grid_6">
		<?php endif ?>

		<?php if (($y == 2) || ($y == 3)): ?>
			<div class="grid_4">
		<?php endif ?>
		<?php if ($y == 4): ?>
			<div class="grid_4">
		<?php endif ?>
		<?php if ($y == 3): ?>
			<?php $y = 1; ?>
		<?php endif ?>
		<div class="p-container roundy_product_home">
					<!-- this is where the items count was -->
					<?php
						//check if top two area, show big splash
						if (($x <= 1)&&(!empty($event->images->splash_big_image))){
							$productImage = "/image/{$event->images->splash_big_image}.jpg";
						}
						//else show small splash
						elseif(!empty($event->images->splash_small_image)){
							$productImage = "/image/{$event->images->splash_small_image}.jpg";
						}
						else {
							$productImage = ($x <= 1) ? "/img/no-image-large.jpeg" : "/img/no-image-small.jpeg";
						}
					?>
					<?php
						if(empty($departments)) {
							$url = $event->url;
						} else {
							$url = $event->url.'/?filter='.$departments;
						}
					?>
					<?php if ($x <= 1): ?>
						<?php echo $this->html->link(
							$this->html->image("$productImage", array(
							'title' => $event->name,
							'alt' => $event->name,
							'width' => '349',
							'height' => '403',
					'style' => 'margin:0px 0px -6px 0px;'
							)), "sale/$url", array('escape'=> false));
						?>
					<?php else: ?>
						<?php echo $this->html->link(
							$this->html->image("$productImage", array(
							'title' => $event->name,
							'alt' => $event->name,
							'width' => '228',
							'height' => '263'
						)), "sale/$url", array('escape'=> false));
						 ?>
					<?php endif ?>

			<div class="splash-details">
						<div class="table-cell left" style="display:block; padding:5px 5px 5px 10px;">
						 <p style="padding:0px; margin:0px; font-size:15px; color:#fff; font-weight:normal; text-transform:none;"> <?php echo $event->name; ?></p>
						 <p style="padding:0px; margin:-3px 0px 0px 0px; font-size:12px; color:#c7c7c7; font-weight:normal; font-style:italic; text-transform:none;"><span id="<?php echo "todaysplash$x"; ?>" title="<?php echo $date = $event->end_date->sec * 1000; ?>" class="counter end"></span>
						</div>

						<div class="table-cell right">
							<?php echo $this->html->link('Shop', 'sale/'.$event->url, array('class' => 'button small', 'style'=>'display:table-cell !important'));?>
						</div>
					</div>
				</div>
			</div>

			<?php if ($x == 1): ?>
				<div id="banner_container" class="grid_5" style="height:405px; overflow:hidden;">
					<div><a href="/users/invite"><img src="/img/invite_girl.png"></a></div>
					<?php if(!empty($banner["img"])): ?>
						<?php foreach($banner["img"] as $image): ?>
							<div><?php if(!empty($image["url"])):?>
								<a href="<?php echo $image["url"]?>"
									<?php
										if(array_key_exists('newPage', $image) && $image['newPage']) {
											echo 'target="_blank"';
										}
									?>
									>
									<img src="/image/<?php echo $image["_id"]?>.jpeg" alt="" />
								</a>
								<?php else: ?>
									<img src="/image/<?php echo $image["_id"]?>.jpeg" alt="" />
								<?php endif ?>
							</div>
						<?php endforeach ?>
					<?php endif ?>
				</div>
				<div class="clear"></div>
			<?php endif ?>
		<?php $x++; ?>
		<?php $y++; ?>
	<?php endforeach ?>

	<div style="margin-bottom:35px;" class="clear"></div>

	<div class="container_16">
	<div class="grid_16">
		<h2 class="page-title gray">Upcoming Sales</h2>
		<hr />
	</div>
		<?php $x = 0; ?>
		<?php $y = 0; ?>
		<?php foreach ($pendingEvents as $event): ?>
			<?php if (($y == 0) || ($y == 2)): ?>
				<div class="grid_4">
			<?php endif ?>
			<?php if ($y == 1): ?>
				<div class="grid_4">
			<?php endif ?>
			<?php if ($y == 2): ?>
				<?php $y = -1; ?>
			<?php endif ?>
					<div class="p-container roundy_product_home">
						<?php
							if (!empty($event->images->splash_small_image)) {
								$productImage = "/image/{$event->images->splash_small_image}.jpg";
							} else {
								$productImage = "/img/no-image-small.jpeg";
							}
						?>
						<?php echo $this->html->link(
						$this->html->image("$productImage", array(
							'title' => $event->name,
							'alt' => $event->name,
							'width' => '228',
							'height' => '263'
						)), 'sale/'.$event->url, array('escape'=> false));
						 ?>
				<div class="splash-details">
						<div class="table-cell left" style="display:block; padding:5px 5px 5px 10px;">
						 <p style="padding:0px; margin:0px; font-size:15px; color:#fff; font-weight:normal; text-transform:none;"> <?php echo $event->name; ?></p>
						 <p style="padding:0px; margin:-3px 0px 0px 0px; font-size:12px; color:#c7c7c7; font-weight:normal; font-style:italic; text-transform:none;">
						 <span id="<?php echo "futuresplash$x"; ?>" title="<?php echo $date = $event->start_date->sec * 1000; ?>" class="counter start"></span>
						</div>

						<div class="table-cell right">
							<?php echo $this->html->link('View', 'sale/'.$event->url, array('class' => 'button small', 'style'=>'display:table-cell !important'));?>
						</div>
					</div>
				</div>
			</div>

			<?php $x++; ?>
			<?php $y++; ?>
	<?php endforeach ?>
	</div>
</div>
</div>
</div>
<div id="modal" style="background:#fff!important;"></div>
<!--Javascript Output for Today's Events -->
<?php if (!empty($todayJs)): ?>
	<?php foreach ($todayJs as $value): ?>
		<?php //echo $value ?>
	<?php endforeach ?>
<?php endif ?>

<!--Javascript Output for Future Events-->
<?php if (!empty($futureJs)): ?>
	<?php foreach ($futureJs as $value): ?>
		<?php //echo $value ?>
	<?php endforeach ?>
<?php endif ?>

<script type="text/javascript">
//<!--
	$(document).ready(function() {
		$("#banner_container").rotate();
	});

	$(".counter").each( function () {

		var fecha  = parseInt(this.title);
		var saleTime = new Date(fecha);
		var now = new Date();
		var diff = saleTime - (now.getTime());

		//check if its and end date or start date
		if($("#" + this.id).hasClass("start"))
		{
		    if((diff / 1000) < (24 * 60 * 60) ) {
		        $("#" + this.id).countdown({until: saleTime, layout: 'Opens in {hnn}{sep}{mnn}{sep}{snn}'});
		    } else {
		        $("#" + this.id).countdown({until: saleTime, layout: 'Opens in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
		    }
		} else {
		    if((diff / 1000) < (24 * 60 * 60) ) {
		    	$("#" + this.id).countdown({until: saleTime, layout: 'Ends in {hnn}{sep}{mnn}{sep}{snn}'});
		    } else {
		    	$("#" + this.id).countdown({until: saleTime, layout: 'Ends in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
		    }
		}
	 });

//-->
</script>
<script type="text/javascript">

	$('#disney').click(function(){
		$('#modal').load('/events/disney').dialog({
			autoOpen: false,
			modal:true,
			width: 739,
			height: 750,
			position: 'top',
			close: function(ev, ui) { $(this).close(); }

		});
		$('#modal').dialog('open');
	});
</script>

<!-- Google Code for inscrits Remarketing List -->
<script type="text/javascript">

/* <![CDATA[ */
	var google_conversion_id = 1019183989;
	var google_conversion_language = "en";
	var google_conversion_format = "3";
	var google_conversion_color = "666666";
	var google_conversion_label = "E1ZLCMH8igIQ9Yb-5QM";
	var google_conversion_value = 0;
/* ]]> */

</script>

<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1019183989/?label=E1ZLCMH8igIQ9Yb-5QM&amp;guid=ON&amp;script=0"/>
	</div>
</noscript>
<!-- END OF Google Code for inscrits Remarketing List -->

<script>
(function($) {
	$.fn.rotate = function() {
		var container = $(this);
		var totale = container.find("div").size();
		var current = 0;
		var i = setInterval(function() {
			if (current >= totale) current = 0;
			container.find("div").filter(":eq("+current+")").fadeIn("slow").end().not(":eq("+current+")").fadeOut("slow");
			current++;
		}, 5000);
		return container;
	};
})(jQuery);
</script>
<script type="text/javascript">
//cto home tag
var cto_conf = 't1=sendEvent&c=2&p=3290';
var cto_conf_event = 'v=2&wi=7714287&pt1=0&pt2=1';
var CRITEO=function(){var b={Load:function(d){var c=window.onload;window.onload=function(){if(c){c()}d()}}};function a(e){if(document.createElement){
var c=document.createElement((typeof(cto_container)!='undefined'&&cto_container=='img')?'img':'iframe');if(c){c.width='1px';c.height='1px';c.style.display='none';
c.src=e;var d=document.getElementById('cto_mg_div');if(d!=null&&d.appendChild){d.appendChild(c)}}}}return{Load:function(c){
document.write("<div id='cto_mg_div' style='display:none;'></div>");c+='&'+cto_conf;var f='';if(typeof(cto_conf_event)!='undefined')f=cto_conf_event;
if(typeof(cto_container)!='undefined'){if(cto_container=='img')c+='&resptype=gif';}if(typeof(cto_params)!='undefined'){for(var key in cto_params){if(key!='kw')
f+='&'+key+'='+encodeURIComponent(cto_params[key]);}if(cto_params['kw']!=undefined)c+='&kw='+encodeURIComponent(cto_params['kw']);}c+='&p1='+encodeURIComponent(f);
c+='&cb='+Math.floor(Math.random()*99999999999);try{c+='&ref='+encodeURIComponent(document.referrer);}catch(e){}try{
c+='&sc_r='+encodeURIComponent(screen.width+'x'+screen.height);}catch(e){}try{c+='&sc_d='+encodeURIComponent(screen.colorDepth);}catch(e){}b.Load(function(){
a(c.substring(0,2000))})}}}();CRITEO.Load(document.location.protocol+'//dis.us.criteo.com/dis/dis.aspx?');
</script>