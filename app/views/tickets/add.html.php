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
			
	<h2 class="gray mar-b">Contact Us</h2>
	<hr />
	
	<?=$this->form->create(); ?>
		<select id="parent" style="width:350px;" name="issue_type">
			<option value="">I need help with:</option>
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
	
		<?=$this->form->submit('Submit', array('class' => "flex-btn" )); ?>
	<?=$this->form->end(); ?>
		
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
 

