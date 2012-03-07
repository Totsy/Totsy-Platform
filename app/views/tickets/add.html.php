<?php $this->title("Contact Us"); ?>
<?php use lithium\storage\Session; ?>
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
			<?php if(Session::read('layout', array('name' => 'default'))!=='mamapedia') : ?>	    
			<li><a href="/pages/affiliates" title="Affiliates"><span>Affiliates</span></a></li>
			<?php endif ?>
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
<div class="grid_8">
	<h2 class="gray mar-b">Contact Us</h2>
	<hr />
	<?php echo $this->form->create(); ?>
	<h3 style="margin:10px auto;" class="gray">Your contact information</h3>
				<?php if (is_array($error) && array_key_exists('firstname',$error)){?>
				<div class="standard-message" style="border:0px!important; background:none!important;">
					<?php echo $error['firstname'][0];?>
				</div>
				<div style="clear:both;"></div>
				<?php } ?>
				<div class="form-row">
				<?php echo $this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('firstname', array('class' => 'inputbox', 'value'=>is_null($data)?$user['firstname']:$data['firstname']));?>  
			</div>
				<?php if (is_array($error) && array_key_exists('lastname',$error)){?>
				<div class="standard-message" style="border:0px!important; background:none!important;">
					<?php echo $error['lastname'][0];?>
				</div>
				<div style="clear:both;"></div>
				<?php } ?>
			<div class="form-row">
				<?php echo $this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('lastname', array('class' => 'inputbox', 'value'=>is_null($data)?$user['lastname']:$data['lastname']));?>  
				
			</div>
			<?php if (is_array($error) && array_key_exists('telephone',$error)){?>
			<div class="standard-message" style="border:0px!important; background:none!important;">
				<?php echo $error['telephone'][0];?>
			</div>
			<div style="clear:both;"></div>
			<?php } ?>
			<div class="form-row">
				<?php echo $this->form->label('telephone', 'Telephone <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('telephone', array('class' => 'inputbox', 'id' => 'phone', 'value'=>is_null($data)?'':$data['telephone']));?> 
			</div>

			<h3 style="margin:10px auto;" class="gray">Please describe your issue below: </h3>
		<select id="parent" style="width:350px;" name="issue_type">
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
		<select id="child" name="type" style="width:350px; display:none;">
		<!-- orders -->
			<?php if ($orders): ?>
				<option value="">Choose Your Order Number</option>
				<?php foreach ($orders as $key => $value): ?>
					<option class="sub_order" value="<?php echo $key?>"> <?php echo $value?></option>
				<?php endforeach ?>
			<?php endif ?>

		<!-- Tech -->
			<option class="sub_tech" value="">Choose One</option>
			<option class="sub_tech" value="Trouble with logging in.">Trouble with logging in.</option>
			<option class="sub_tech" value="My cart items have expired.">My cart items have expired.</option>

		<!-- Refund -->
			<option class="sub_refunds" value="">Choose One</option>
			<option class="sub_refunds" value="I would like a refund">I would like a refund</option>
			<option class="sub_refunds" value="What happened to my credits?">What happened to my credits?</option>

		<!-- Merch -->
			<option class="sub_merch" value="">Choose One</option>
			<option class="sub_merch" value="Selling products on Totsy">Selling products on Totsy</option>
			<option class="sub_merch" value="Merchandising information">Merchandising information</option>

		<!-- Shipping -->
			<option class="sub_shipping" value="">Choose One</option>
			<option class="sub_shipping" value="I would like a return">I would like a return</option>
			<option class="sub_shipping" value="Where is my order?">Where is my order?</option>

		<!-- Business Dev -->
			<option class="sub_business" value="">Choose One</option>
			<option class="sub_business" value="How can I sell my products on Totsy?">How can I sell my products on Totsy?</option>
			<option class="sub_business" value="Can I run an exclusive sale on Totsy?">Can I run an exclusive sale on Totsy?</option>

		<!-- Press -->
			<option class="sub_press" value="">Choose One</option>
			<option class="sub_press" value="I would like more information about Totsy.">I would like more information about Totsy.</option>
			<option class="sub_press" value="I'd like a media kit">I'd like a media kit</option>
		</select>

		<br />
		<h3 style="margin:10px auto;" class="gray">Your Message</h3>
		<?php if (is_array($error) && array_key_exists('message',$error)){?>
			<div class="standard-message" style="border:0px!important; background:none!important;">
					<?php echo $error['message'][0];?>
				</div>
		<?php } ?>	
		<?php echo $this->form->textarea('message', array(
			'class' => 'inputbox',
			'style' => 'width:300px;height:120px'
		));
		?>
		<br />

		<?php echo $this->form->submit('Send Information', array('class' => "button" )); ?>
	<?php echo $this->form->end(); ?>
</div>
<div id="message" class="grid_3">
				<h2 class="gray">Corporate Address</h2>
				<hr />
				10 West 18th Street<br/>
				4th Floor<br/>
				New York, NY 10011<br/>
				<br />
				<h3 class="gray">Contact Support</h3>
				<a href="mailto:support@totsy.com">support@totsy.com</a><br />
				888-247-9444<br />
				Office Hours:<br/> M-F 9am - 7pm EST</p>
	</div>	
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
          $("#uniform-child").hide();
          $("input[type=Submit]").attr("disabled","disabled");
        } else {
          $("#uniform-child").show().children('select').show();
          $("input[type=Submit]").removeAttr("disabled");
        };
      }
    });
  </script> 
