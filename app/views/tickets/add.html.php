<?php $this->title("Contact Us"); ?>

<div class="grid_16">
	<h2 class="page-title gray">Contact Us</h2>
	<hr />
</div>

<div class="grid_4">
	<div class="roundy grey_inside">
		<h3 class="gray">About Us</h3>
		<hr />
		<ul class="menu main-nav">
		    <li class="active"><a href="/pages/aboutus" title="About Totsy">How Totsy Works</a></li>
		    <li><a href="/pages/moms" title="Meet The Moms">Meet The Moms</a></li>
		    <li><a href="/pages/press" title="Press">Totsy in the Press</a></li>
		    <li><a href="/pages/testimonials" title="Video Testimonials">Video Testimonials</a></li>
		    <li><a href="/pages/being_green" title="Being Green">Being Green</a></li>
		    <li><a href="http://blog.totsy.com" target="_blank" title="Blog">Totsy Blog</a></li>
		    <li><a href="/pages/affiliates" title="Affiliates"><span>Affiliates</span></a></li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Need Help?</h3>
		<hr />
		<ul class="menu main-nav">
		    <li><a href="/tickets/add" title="Contact Us">Help Desk</a></li>
			<li><a href="/pages/faq" title="Frequently Asked Questions">FAQ's</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/terms" title="Terms Of Use">Terms Of Use</a></li>
		</ul>
	</div>
</div>

<div class="grid_11 omega roundy grey_inside b_side">
	<h2 class="gray mar-b">Contact Us</h2>
	<hr />

	<div style="float:left; width:330px;">
	<div id="hidden-div" style="display:none; color:#eb132c; font-weight:bold;">Please Choose A Help Topic </div>
	<?=$this->form->create(); ?>
		<select id="parent" name="issue_type">
			<option value="default">I need help with:</option>
			<?php if ($orders): ?>
				<option value="order">My Order(s)</option>
			<?php endif ?>
			<option value="tech">Technical Support</option>
			<option value="refunds">Refund &amp; Credit</option>
			<option value="merch">Merchandising &amp; Product Inquiries</option>
			<option value="shipping">Shipping &amp; Returns</option>
			<option value="business">Business Development &amp; Vendor Opportunities</option>
			<option value="press">Press Inquiries</option>
		</select>

		<br /><br />
		<select id="child" name="type" style="width:350px;">
		<!-- orders -->
			<?php if ($orders): ?>
				<option value="">Choose Your Order Number</option>
				<?php foreach ($orders as $key => $value): ?>
					<option class="sub_order" value="<?=$key?>"><?=$value?></option>
				<?php endforeach ?>
			<?php endif ?>

		<!-- Tech -->
			<option class="sub_tech" value="">Choose One</option>
			<option class="sub_tech" value="5">Trouble with logging in.</option>
			<option class="sub_tech" value="5">My cart items have expired.</option>

		<!-- Refund -->
			<option class="sub_refunds" value="">Choose One</option>
			<option class="sub_refunds" value="6">I would like a refund</option>
			<option class="sub_refunds" value="7">What happened to my credits?</option>

		<!-- Merch -->
			<option class="sub_merch" value="">Choose One</option>
			<option class="sub_merch" value="6">Selling products on Totsy</option>
			<option class="sub_merch" value="7">Merchandising information</option>

		<!-- Shipping -->
			<option class="sub_shipping" value="">Choose One</option>
			<option class="sub_shipping" value="6">I would like a return</option>
			<option class="sub_shipping" value="7">Where is my order?</option>

		<!-- Business Dev -->
			<option class="sub_business" value="">Choose One</option>
			<option class="sub_business" value="6">How can I sell my products on Totsy?</option>
			<option class="sub_business" value="7">Can I run an exclusive sale on Totsy?</option>

		<!-- Press -->
			<option class="sub_press" value="">Choose One</option>
			<option class="sub_press" value="6">I would like more information about Totsy.</option>
			<option class="sub_press" value="7">I'd like a media kit</option>
		</select>

		<br />

		<h3>Your Message</h3>
		<?=$this->form->textarea('message', array(
			'class' => 'inputbox',
			'style' => 'width:300px;height:120px'
		));
		?>
		<br />

		<?=$this->form->submit('Send Information', array('class' => "button" )); ?>
	<?=$this->form->end(); ?>
</div>
	<div id="message" style="float:left; width:150px; margin-left:20px;">
				<strong>Corporate Address:</strong><br/>
				10 West 18th Street<br/>
				4th Floor<br/>
				New York, NY 10011<br/>
				<br />
				<h3 class="gray">Contact Support</h3>
				<a href="mailto:support@totsy.com">support@totsy.com</a><br />
				888-247-9444<br />
				Office Hours:<br/> M-F 10am - 5pm EST</p>
	</div>
</div>


</div>
<div class="clear"></div>


<script language="javascript">
function makeSublist(parent,child,isSubselectOptional,childVal)
{
	$("body").append("<select style='display:none' id='"+parent+child+"'></select>");
	$('#'+parent+child).html($("#"+child+" option"));

		var parentValue = $('#'+parent).attr('value');
		$('#'+child).html($("#"+parent+child+" .sub_"+parentValue).clone());

	childVal = (typeof childVal == "undefined")? "" : childVal ;
	$("#"+child+' option[@value="'+ childVal +'"]').attr('selected','selected');

	$('#'+parent).change(
		function()
		{
			var parentValue = $('#'+parent).attr('value');
			$('#'+child).html($("#"+parent+child+" .sub_"+parentValue).clone());
			if(isSubselectOptional) $('#'+child).prepend("<option value='none'> -- Select -- </option>");
			$('#'+child).trigger("change");
                        $('#'+child).focus();
		}
	);
}

	$(document).ready(function()
	{
		makeSublist('child','grandsun', true, '');
		makeSublist('parent','child', false, '1');
	});
</script>



  <script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
      checkOptions();
      $("select").change(checkOptions);

      function checkOptions() {
        var getSize = false;
        $("select").each(function(index, element) {
          if ( $(element).val() == "default" ) {
            getSize = true;
          }
        });
        
        if (getSize) {
          $("#hidden-div").show();
          $("input[type=Submit]").attr("disabled","disabled");
        } else {
          $("#hidden-div").hide();
          $("input[type=Submit]").removeAttr("disabled");
        };
      }
    });
  </script> 