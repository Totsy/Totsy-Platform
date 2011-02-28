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
		<select id="parent" style="width:350px;" name="title">
			<option value="">I need help with:</option>
			<option value="1">My Order(s)</option>
			<option value="2">Technical Support</option>
			<option value="3">Customer Support</option>
			<option value="4">New Business Development</option>
			<option value="5">Press</option>
		</select>
	
		<br />
		<select id="child" style="width:350px;">
			<option value="">Choose Your Order Number</option>
			<option class="sub_1" value="2356">Order #2356 - Ed Hardy Shoes</option>
			<option class="sub_1" value="2357">Order #2357 - All Pop Art</option>
			<option class="sub_1" value="2358">Order #2358 - Some Other Brand</option>
			<option class="sub_1" value="2359">Order #2359 - Here's Another One</option>
	
			<option class="sub_1" value="2360">Order #2360 - You Get The Idea</option>
			<option class="sub_1" value="2361">Order #2361 - You Get The Idea Right?</option>
			<option class="sub_1" value="2362">Order #2362 - You SEE?</option>
			<option class="sub_1" value="2363">Order #2363 - Hello?</option>
	
			<option class="sub_2" value="">Choose One</option>
			<option class="sub_2" value="5">Trouble with logging in</option>
	
			<option class="sub_2" value="5">Where Are My Credits</option>
			<option class="sub_2" value="5">Problem With Shipping</option>
		
			<option class="sub_3" value="">Choose One</option>
			<option class="sub_3" value="6">Phone Number</option>
			<option class="sub_3" value="7">Walk In</option>
			
			<option class="sub_4" value="">Choose One</option>
	
			<option class="sub_4" value="6">Phone Number</option>
			<option class="sub_4" value="7">Walk In</option>
			
			<option class="sub_5" value="">Choose One</option>
			<option class="sub_5" value="6">Phone Number</option>
			<option class="sub_5" value="7">Walk In</option>
		</select>
	
		<br />
		<h3>Users Details</h3>
		<input type="text" disabled="disabled" value="<?=$userInfo['firstname']?> <?=$userInfo['lastname']?>" />
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
 

