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


		<?php if (sizeof($creditcards) > 0) { ?> 
			<?php $x = 0?>
			<?php foreach ($creditcards as $creditcard): ?>
				<?php $x++; ?>
				
				<div style="border: 1px solid #000; " id=<?php echo $creditcard[profileId];?>>

<table width="650" border="0" cellspacing="0" cellpadding="0" style="margin: 10px;">
  <tr>
    <td width="10%" align="left">
   	<?php
 		switch ($creditcard[type]) {
 			case 'American Express': 
 				$type = "cc_amex.gif"; 
 				$cc_name = "American Express";
 			break;
 			case 'Visa': 
 				$type = "cc_visa.gif"; 
 				$cc_name = "Visa";
 			break;
 			case 'Mastercard': 
 				$type = "cc_mastercard.gif"; 
				$cc_name = "Mastercard";
 			break;
 		}
   	
   	?>
	<img src="/img/<?php echo $type;?>">   	
   	</td>
    <td width="67%">
    <?php echo $cc_name?> ending in <strong><?php echo substr($creditcard[number], -4);?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;expires <strong><?php echo $creditcard[month];?>/<?php echo $creditcard[year];?></strong></td>
    <td width="13%" align="right"><a href="#" id="remove_<?php echo $creditcard[profileId]?>" title="Remove Credit Card" class="creditcard_remove">Delete</a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td rowspan="2">
    <br/>
    						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td width="141" valign="top">Cardholder Name:</td>
								
								<td width="281"><?php echo $creditcard[firstname];?> <?php echo $creditcard[lastname];?></td>
							</tr>
							<tr>
								<td valign="top">Billing Address:</td>
								<td><?php echo $creditcard[address];?><br/>	
									<?php if($creditcard[address2]): ?>
										<?php echo $creditcard[address2]?><br />
									<?php endif ?>
									<?php echo $creditcard[city];?>, <?php echo $creditcard[state];?> <?php echo $creditcard[zip];?>
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
			<?php endforeach ?>
		<?php } else { ?>
		<div style="text-align:center;">You don't have any saved credit cards.</div>
		<?php } ?>
</div>


	<br />
</div>
</div>
<div class="clear"></div>


<script type="text/javascript">

	$(document).ready( function() {
		$(".creditcard_remove").each( function() {	
		
			var creditcard_id = this.id.replace("remove_", "");
		
			$('#' + this.id + "").click ( function () {
			
				var remove = confirm ("Are you sure you want to remove this credit card?"); 
				
				if ( remove ) {					
			    	$('#' + creditcard_id + "").remove();
			    	$.ajax({ url: $.base + "creditcards/remove_creditcard", 
			    			 data: creditcard_id, 
			    			 context: document.body, 
			    			 success: function(data) {
			    				//
			    	      	 }
					});
				} else {
					return false;
				}	
			});
		});
	});
	
</script>