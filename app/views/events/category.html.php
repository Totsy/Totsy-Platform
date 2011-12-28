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
	<h2 class="page-title gray"> <?php echo $categories; ?> <em>(<?php echo $eventCount; ?> Sales Found)</em></h2>
	<div id="events-wrap" class="fullwidth">
		<?php $x = 0; ?>
		<?php foreach ($openEvents as $event): ?>
		
		<div class="event grid-x">
			<div class="eventDetails group">
				<h3><?php echo $event['name']; ?> <em id="<?php echo "todaysplash$x"; ?>" title="<?php echo $date = $event['end_date'] *1000 ; ?>" class="counter end"></em><!-- @TODO - use data-attribute instead of title… better, use a date/time element instead of an em --></h3>
				<div>
					<?php
					
					// store event image
						if(!empty($event['images']['event_image'])) {
							$eventImage = "/image/{$event['images']['event_image']}.jpg";
						}
						else {
							$eventImage = "img/no-image-small.jpeg";
						}
	
					// build link and image
						$url = $event['url'];
						echo $this->html->link(
							$this->html->image("$eventImage", array(
								'title' => $event['name'],
								'alt' => $event['name'],
								'width' => '126',
								'height' => '81'
							)), "sale/$url", array('escape'=> false)
						);
					?>
				</div>
				<p><?php echo strip_tags($event['blurb']); ?></p>
			</div><!-- /.eventDetails -->
			
			<div class="items group">
			<?php
			//print_r($event->eventItems);
			$items = $event['eventItems'];
			
			foreach($items as $item){ ?>
				<div class="item">
					<a href="<?php echo 'sale/' . $item['url']?>" title="<?php echo $item['description'];?>">
						<img width="125" height="126" src="<?php echo "http://www.totsy.com/image/" . $item['primary_image'] . ".jpg";?>" alt="IMAGE ALT HERE" />
						<h4><?php echo $item['description'];?></h4>
						<p>$<?php echo $item['sale_retail'];?></p>
					<a></a>
				</div><!-- /.item -->
			<?php
			}
			?>
				<div class="btn viewAllEvents">
					<?php
						echo $this->html->link(
							$this->html->image("/img/btn-viewevents-bug.png", array(
								'title' => 'View all items from this sale',
								'alt' => 'View all items from this sale',
								'width' => '135',
								'height' => '189'
							)), 'sale/'.$event->url, array('escape'=> false)
						);
					?>
				</div>
			</div><!-- /.items -->
			<p>View all items from <?php echo $this->html->link($event['name'], 'sale/'.$event['url'], array('escape'=> false) );?></p>
		</div><!-- /.event -->
		<?php $x++; ?>
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
		    	$("#" + this.id).countdown({until: saleTime, layout: 'Ends in <strong>{hnn}{sep}{mnn}{sep}{snn}</strong>'});
		    } else {
		    	$("#" + this.id).countdown({until: saleTime, layout: 'Ends in <strong>{dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}</strong>'});
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