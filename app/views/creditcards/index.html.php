<?php $this->title("Credit Cards"); ?>

<div class="grid_16">
	<h2 class="page-title gray">Credit Cards &amp; Billing</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>

<div class="grid_11 omega roundy grey_inside b_side">


	<h2 class="page-title gray">Credit Cards &amp; Billing</h2>

	<div style="float: right; margin-top: -20px;"><a href="/creditcards/add">Add a new credit card</a></div>
	<hr />

<?php if ($message) { ?>
	<div class="checkout-error"><h2><?php echo $message;?></h2></div>
<?php } ?>


		<?php if ($cyberSourceProfiles) { ?> 
			<?php $x = 0?>
			<?php foreach ($cyberSourceProfiles as $cyberSourceProfile): ?>
				<?php if($cyberSourceProfile[savedByUser]): ?>
				<?php $x++; ?>
				
				<div style="border: 1px solid #DDD; " id=<?php echo $cyberSourceProfile[profileID];?>>

<table width="650" border="0" cellspacing="0" cellpadding="0" style="margin: 10px;">
  <tr>
    <td width="10%" align="left">
   	<?php
 		switch ($cyberSourceProfile[creditCard][type]) {
 			case 'amex': 
 				$type = "/img/cc_amex.gif"; 
 				$cc_name = "American Express";
 			break;
 			case 'visa': 
 				$type = "/img/cc_visa.gif"; 
 				$cc_name = "Visa";
 			break;
 			case 'mastercard': 
 				$type = "/img/cc_mastercard.gif"; 
				$cc_name = "MasterCard";
 			break;
 			case 'mc': 
 				$type = "/img/cc_mastercard.gif"; 
				$cc_name = "MasterCard";
 			break;
 		}
   	
   	?>
	<img src="<?php echo $type;?>">   	
   	</td>
    <td width="67%">
		<?php echo $cc_name?> ending in <strong><?php echo $cyberSourceProfile[creditCard][number];?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    						expires <strong><?php echo $cyberSourceProfile[creditCard][month];?>/<?php echo $cyberSourceProfile[creditCard][year];?></strong>
    </td>
    <td width="13%" align="right"><a href="/creditcards/remove?profileID=<?php echo $cyberSourceProfile[profileID];?>" id="remove_<?php echo $cyberSourceProfile[profileID]?>" title="Remove Credit Card" class="creditcard_remove">Delete</a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td rowspan="2">
    <br/>
    						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td width="141" valign="top">Cardholder Name:</td>
								
								<td width="281"><?php echo $cyberSourceProfile[billing][firstName];?> <?php echo $cyberSourceProfile[billing][lastName];?></td>
							</tr>
							<tr>
								<td valign="top">Billing Address:</td>
								<td><?php echo $cyberSourceProfile[billing][address];?><br/>	
									<?php if($cyberSourceProfile[billing][address2]): ?>
										<?php echo $cyberSourceProfile[billing][address2]?><br />
									<?php endif ?>
									<?php echo $cyberSourceProfile[billing][city];?>, <?php echo $cyberSourceProfile[billing][state];?> <?php echo $cyberSourceProfile[billing][zip];?>
</td>
							</tr>							
						</table>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
			<br/>
			<?php endif; ?>
			<?php endforeach; ?>
		<?php } ?>
				
		<?php if ($x == 0 || !$cyberSourceProfiles) { ?>
		<div style="text-align:center;">You don't have any saved credit cards.</div>
		<?php } ?>
</div>


	<br />
</div>
</div>
<div class="clear"></div>