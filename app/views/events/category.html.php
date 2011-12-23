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
CATEGORY VIEW
<div class="fullwidth">
	<?php foreach ($openEvents as $event): ?>
	
		<div class="event grid-x" style="outline:2px dotted #c00; width:930px; overflow:hidden;">
			<div class="eventDetails">
				<h3><?php echo $event->name; ?></h3>
				<em data-date="<?php echo $date = $event->end_date->sec * 1000; ?>" class="counter end">*counter here*</em><!-- @TODO - use date/time element? -->
				<p><?php echo $event['blurb']; ?></p>
				<div>
					<?php
						if (!empty($event->images->splash_big_image)) {
							$productImage = "/image/{$event->images->splash_big_image}.jpg";
						} else {
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
							'height' => '266'
						)), "sale/$url", array('escape'=> false));
						 ?>
					<?php endif ?>
				</div>
			</div><!-- /.eventDetails -->
			
		<?php
			//print_r($event->eventItems);
			$items = $event->eventItems;
	
			foreach($items as $item){ ?>
				<div class="item" style="float:left; margin-right:7px; width:125px; height:188px; border:1px solid #ccc;">
					<img width="126" height="126" src="<?php echo "http://www.totsy.com/image/" . $item['primary_image'] . ".jpg";?>" alt="IMAGE ALT HERE" />
					<h4><?php echo $item['description'];?></h4>
					<p>$<?php echo $item['sale_retail'];?></p>
			<?php	
			/*
			
				echo "<!--";
				echo "<pre>";
				print_r($item);
				echo "</pre>";
				echo "-->";
	
				echo "<br>";
				echo "id is " . $item['_id'];
				echo "<br>";
	
				echo "price is " . $item['sale_retail'];
				echo "<br>";
	
				echo "title is  " . $item['description'];
				echo "<br>";
				
				echo "url slug is  " . $item['url'];
				echo "<br>";
				echo "so full clickthru is /sale/" . $item['url'];
				echo "<br>";
				
				
				//var is just the jpg filename, hardcoded to www.totsy bc dev often doesnt have images
				echo "http://www.totsy.com/image/" . $item['primary_image'] . ".jpg";
				echo "<br>";
				echo "<br>";
				echo "<br>";
			*/
			?>
				</div><!-- /.item -->
			<?php
			}
		?>
			<div class="btn viewAllEvents">
				<?php echo $this->html->link('Shop', 'sale/'.$event->url, array('class' => 'button small', 'style'=>'display:table-cell !important'));?>
			</div>

		</div><!-- /.event -->
	
		<?php endforeach ?>

	<div style="margin-bottom:35px;" class="clear"></div>

</div><!-- /.full-width -->
</div><!-- b -->
</div><!-- c -->
<div id="modal" style="background:#fff!important;"></div>

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