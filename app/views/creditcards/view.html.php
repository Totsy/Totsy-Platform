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
		<?php if (sizeof($creditcards) > 0) { ?> 
			<?php $x = 0?>
			<?php foreach ($creditcards as $creditcard): ?>
				<?php $x++; ?>
		<div class="col-2"  id="<?=$creditcard->_id?>">
			<div class="r-container box-2 fl">
				<div class="tl"></div>
				<div class="tr"></div>
				<div class="r-box lt-gradient-1">
					<h3 class="gray fl">Visa ending in <?=substr($creditcard->number, -4);?>, expires <?=$creditcard->month;?>/<?=$creditcard->year;?> </h3>
					
					&nbsp;|&nbsp;<a href="#" id="remove_<?=$creditcard->_id?>" title="Remove Credit Card" class="creditcard_remove">Remove</a>


					<div>
						<table>
							<tr>
								<td width="120px" valign="top">Cardholder Name:</td>
								
								<td><?=$creditcard->firstname;?> <?=$creditcard->lastname;?></td>
							</tr>
							<tr>
								<td valign="top">Billing Address:</td>
								<td><?=$creditcard->address;?><br/>	
									<?php if($creditcard->address2): ?>
										<?=$creditcard->address2?><br />
									<?php endif ?>
									<?=$creditcard->city;?>, <?=$creditcard->state;?> <?=$creditcard->zip;?>
</td>
							</tr>							
						</table>
					</div>
				
				</div>
				<div class="bl"></div>
				<div class="br"></div>
			</div>
		
		</div>

			<?php endforeach ?>
		<?php } else { ?>
		<div style="text-align:center;">You don't have any saved credit cards yet.</div>
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
			    	$.ajax({ url: $.base + "creditcards/remove", 
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