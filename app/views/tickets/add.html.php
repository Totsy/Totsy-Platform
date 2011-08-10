<?php if (!empty($userInfo)): ?>

			<h1 class="p-header"><?=$this->title("Contact Us"); ?></h1>

<div id="left">
		<ul class="menu main-nav">
		  <h3 style="color:#999;">About Us</h3>
		  <hr />
		  <li class="first item15"><a href="/pages/aboutus" title="About Totsy"><span>How Totsy Works</span></a></li>
		  <li class="first item17"><a href="/pages/moms" title="Meet The Moms"><span>Meet The Moms</span></a></li>
		  <li class="first item16"><a href="/pages/press" title="Press"><span>Totsy in the Press</span></a></li>
		  <li class="first item17"><a href="/pages/being_green" title="Being Green"><span>Being Green</span></a></li>
		  <li class="first"><a href="/pages/testimonials" title="Video Testimonials"><span>Video Testimonials</span></a></li>
          <li class="first item19"><a href="http://blog.totsy.com" target="_blank" title="Blog"><span>Totsy Blog</span></a></li>
          <li class="first item15"><a href="/pages/affiliates" title="Affiliates"><span>Affiliates</span></a></li>
		  <br />
		  <h3 style="color:#999;">Need Help?</h3>
		  <hr />
		  <li class="first item18 active"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
		  <li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
		</ul>
	</div>
<?php endif ?>

<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">

	<div style="float:left; width:350px;">

	<h2 class="gray mar-b">Contact Us</h2>
	<hr />

	<?=$this->form->create(); ?>
	<h3 style="margin:10px auto;" class="gray">Your contact information</h3>
	<div class="form-row">
				<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('firstname', array('class' => 'inputbox', 'value'=>is_null($data)?$user['firstname']:data['firstname']));?>  
				<? if (is_array($error) && array_key_exists('firstname',$error)){?>
					<?=$error['firstname'][0];?>
				<? } ?>
				<? //=$this->form->error('firstname'); ?>
			</div>

			<div class="form-row">
				<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('lastname', array('class' => 'inputbox', 'value'=>is_null($data)?$user['lastname']:data['lastname']));?>  
				<? if (is_array($error) && array_key_exists('lastname',$error)){?>
					<?=$error['lastname'][0];?>
				<? } ?>
				<? //=$this->form->error('lastname'); ?>
			</div>

			<div class="form-row">
				<?=$this->form->label('telephone', 'Telephone <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('firstname', array('class' => 'inputbox', 'id' => 'phone', 'value'=>is_null($data)?$user['telephone']:data['telephone']));?> 
				<? if (is_array($error) && array_key_exists('telephone',$error)){?>
					<?=$error['firstname'][0];?>
				<? } ?>				
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

		<br />
		<select id="child" name="type" style="width:350px;">
		<!-- orders -->
			<?php if ($orders): ?>
				<option value="">Choose Your Order Number</option>
				<?php foreach ($orders as $key => $value): ?>
					<option class="sub_order" value="<?=$key?>"> <?=$value?></option>
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

	<div class="bl"></div>
	<div class="br"></div>
</div>

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


