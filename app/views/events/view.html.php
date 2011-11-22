<?php $this->title($event->name); ?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>
<div class="grid_16">
		<h2 class="page-title gray"><span class="red"><a href="/" title="Sales"><?=$type?> Sales</a> /</span> <?=$event->name; ?> <div id="listingCountdown" class="listingCountdown" style="float:right;"></div></h2>
		<hr />
<div class="md-gray" style="overflow:hidden; border: 1px solid #D7D7D7;  margin-bottom:10px">
				<div class="grid_5 alpha omega" style="line-height:0px!important;">
					<!-- Display Event Image -->
					<?php
						if (!empty($event->images->event_image)) {
							echo $this->html->image("/image/{$event->images->event_image}.jpg", array(
								'title' => $event->name,
								'width' => "280",
							));
						} else {
							echo $this->html->image('/img/no-image-small.jpeg', array(
									'title' => "No Image Available",
									'width' => "280",
									));
						}
					?>
					
				</div>
				
				<?php if (!empty($event->images->logo_image)) { ?>
				<div class="grid_8 omega" style="padding:10px 0px;">
					<div class="grid_8 alpha omega blurb">
					
					<?php } else { ?>
					<div class="grid_11 omega">
					<div class="grid_11 alpha omega blurb">
					
					<?php } ?>
					
					<?php if (!empty($event->blurb)): ?>
						<?php echo $event->blurb ?>
					<?php endif ?>
					</div>
				
					
</div>
<?php if (!empty($event->images->logo_image)): ?>
<div class="grid_3 alpha omega" style="margin-top:10px;">
						<!-- Display Logo Image -->
						
							<img src="/image/<?=$event->images->logo_image?>.gif" alt="<?= $event->name; ?>" title="<?= $event->name; ?>" width="148" height="52" />
						
					</div>
<?php endif ?>
	<?php
	if($missChristmasCount>0){
	?>
				<div style="margin-top:10px;line-height:12px;font-weight:bold; color:#990000; font-size:11px;text-align:left;">
				<img src="/img/truck_red.png">
				Items in this sale are not guaranteed to arrive before 12/26.*
				</div>
	
	
	<?php
	}
	else{
	?>
				<div style="margin-top:10px;line-height:12px;font-weight:bold; color:#999999; font-size:11px;text-align:left;">
				<img src="/img/truck_grey.png">
				Items will be delivered on or before 12/23.*
				</div>
	
	
	<?php
	}
	?>

			</div>
		</div>
		<br />
			<div class="grid_13 omega" style="text-align:left; height:34px"><?php echo $spinback_fb; ?></div>
			<div class="grid_3" style="text-align:right; margin:0px 5px 10px 0px;">
			<!-- div class="sort-by" -->
			<!-- select id="by-category" name="by-category">
				<option value="">View By Category</option>
				<option value="Strollers">Strollers</option>
				<option value="Accessories">Accessories</option>
			</select>

			<select id="by-size" name="by-size">
				<option value="">View By Size</option>
				<option value="Small">Small</option>
				<option value="Medium">Medium</option>
				<option value="Large">Large</option>
			</select -->
			<?php if(!empty($filters)): ?>
		<div id='filterb' style='text-align:right'>
			<?=$this->form->create(null, array('id' => 'filterform')); ?>
			<label style="font-weight:bold; font-size:13px;">View by: &nbsp;</label>
			<?=$this->form->select('filterby',$filters, array('onchange' => "filter()", 'id' => 'filterby', 'value' => array($departments => $departments))); ?>
			<?=$this->form->end(); ?>
		</div>
		<?php endif ?>
		</div>
		<br />
		<?php if (!empty($items)): ?>
			<?php $y = 0; ?>
			<?php foreach ($items as $item): ?>
				<?php
					if (!empty($item->primary_image)) {
						$image = $item->primary_image;
						$productImage = "/image/$image.jpg";
					} else {
						$productImage = "/img/no-image-small.jpeg";
					}
				?>
				<!-- Start the product loop to output all products in this view -->
				<!-- Start product item -->
					<?php if (($y == 0) || ($y == 2)): ?>
						<div class="grid_4_hack">
					<?php endif ?>
					<?php if ($y == 1): ?>
						<div class="grid_4_hack">
					<?php endif ?>
					<?php if ($y == 2): ?>
						<?php $y = -1; ?>
					<?php endif ?>
					<div class="md-gray p-container roundy_product">
						<?php if ($item->total_quantity <= 0): ?>
								<?=$this->html->image('/img/soldout.png', array(
									'title' => "Sold Out",
									'style' => 'z-index : 99999; position : absolute; right:0;'
								)); ?>
						<?php endif ?>
						<?=$this->html->link(
							$this->html->image($productImage, array(
								'alt' => $item->name,
								'title' => $item->name,
								'width' => '310')),
							"sale/$event->url/{$item->url}",
							array('title' => $item->name, 'escape' => false)
						); ?>
						
						
								<table style="margin:5px;">
									<tr>
										<td width="227" valign="top">
											<a href="<?="/sale/$event->url/$item->url"?>"><h2><?=$item->description ?></h2></a>
										</td>
										<td align="right">
											<span class="price" style="text-transform:uppercase; font-weight:normal; font-size:20px; color: #009900; float:right;">$<?=number_format($item->sale_retail,2);?></span><br>
											<span class="original-price" style="font-size:10px; white-space:nowrap;">Original $<?=number_format($item->msrp,2);?></span>
										</td>
									</tr>
								</table>
								
					</div>
				</div>
				<?php $y++ ?>
				<!-- End product item -->
			<?php endforeach ?>
		<?php endif ?>
<div class="clear"></div>
<div style="color:#707070; font-size:12px; font-weight:bold; padding:10px;">
				<?php
				if($missChristmasCount>0){
				?>
				* Totsy ships all items complete and does not split items into separate orders. If you would like the designated items in your cart delivered on or before 12/23, we suggest you order them separately from the items not guaranteed to arrive before 12/26. Our delivery guarantee does not apply when transportation networks are affected by weather. Please contact our Customer Service department at 888-247-9444 or email <a href="mailto:support@totsy.com">support@totsy.com</a> with any questions.
				
				<?php
				}
				else{
				?>
				
				* Our delivery guarantee does not apply when transportation networks are affected by weather.
				
				<?php
				}
				?>
				
</div>


	</div>
</div>
</div>
<script type="text/javascript">
$(function () {
	var saleStart = new Date();
	var saleEnd = new Date();
	var now = new Date();
	saleStart = new Date(<?php echo $event->start_date->sec * 1000?>);
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	if((now.getTime()) < <?php echo $event->start_date->sec * 1000 ?>) {
		var diff = <?php echo $event->start_date->sec * 1000 ?> - (now.getTime());
		if((diff / 1000) < (24 * 60 * 60) ) {
			$('#listingCountdown').countdown({until: saleStart, layout: 'Opens in {hnn}{sep}{mnn}{sep}{snn}'});
		} else {
			$('#listingCountdown').countdown({until: saleStart, layout: 'Opens in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
		}
	} else {
			var diff = <?php echo $event->end_date->sec * 1000 ?> - (now.getTime());
			if((diff / 1000) < (24 * 60 * 60) ) {
				$('#listingCountdown').countdown({until: saleEnd, layout: 'Ends in {hnn}{sep}{mnn}{sep}{snn}'});
			} else {
				$('#listingCountdown').countdown({until: saleEnd, layout: 'Ends in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
			}
	}
});
function filter() {
	var filter = $('#filterby').val();
	_gaq.push(['_setAccount', 'UA-675412-15']);
	_gaq.push(['_trackPageview']);
	_gaq.push(['_trackEvent', 'departments', 'dropdown', filter]);
	$('#filterform').submit();
};
</script>

<script type="text/javascript">
var cto_params = [];
cto_params["kw"] = "<?=$event->name?>"; //REMOVE LINE IF NOT APPLICABLE
<?php if (!empty($items)): ?>
<?php $iCounter = 1; ?>
<?php foreach ($items as $item): ?>
cto_params["i<?=$iCounter;?>"] = "<?php echo (string) $item->_id; ?>";
<?php if($iCounter==5) break; ?>
<?php $iCounter++; ?>
<?php endforeach ?>
<?php endif ?>
var cto_conf = 't1=sendEvent&c=2&p=3290';
var cto_conf_event = 'v=2&wi=7714287&pt1=3';
var CRITEO=function(){var b={Load:function(d){var c=window.onload;window.onload=function(){if(c){c()}d()}}};function a(e){if(document.createElement){
var c=document.createElement((typeof(cto_container)!='undefined'&&cto_container=='img')?'img':'iframe');if(c){c.width='1px';c.height='1px';c.style.display='none';
c.src=e;var d=document.getElementById('cto_mg_div');if(d!=null&&d.appendChild){d.appendChild(c)}}}}return{Load:function(c){
document.write("<div id='cto_mg_div' style='display:none;'></div>");c+='&'+cto_conf;var f='';if(typeof(cto_conf_event)!='undefined')f=cto_conf_event;
if(typeof(cto_container)!='undefined'){if(cto_container=='img')c+='&resptype=gif';}if(typeof(cto_params)!='undefined'){for(var key in cto_params){if(key!='kw')
f+='&'+key+'='+encodeURIComponent(cto_params[key]);}if(cto_params['kw']!=undefined)c+='&kw='+encodeURIComponent(cto_params['kw']);}c+='&p1='+encodeURIComponent(f)
;
c+='&cb='+Math.floor(Math.random()*99999999999);try{c+='&ref='+encodeURIComponent(document.referrer);}catch(e){}try{
c+='&sc_r='+encodeURIComponent(screen.width+'x'+screen.height);}catch(e){}try{c+='&sc_d='+encodeURIComponent(screen.colorDepth);}catch(e){}b.Load(function(){
a(c.substring(0,2000))})}}}();CRITEO.Load(document.location.protocol+'//dis.us.criteo.com/dis/dis.aspx?');
</script> 
