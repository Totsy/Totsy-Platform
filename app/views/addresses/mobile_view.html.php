<?php $this->title("Address Book"); ?>

	<h2>Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?=$this->html->link('Add New Address','Addresses::add'); ?></span></h2>
	<hr />
		<?php if (!empty($addresses)): ?> 
			
			<?php $x = 0?>
			<?php foreach ($addresses as $address): ?>
				<?php $x++; ?>
				<div id="<?=$address->_id?>">
						<strong>Description:</strong> <?=$address->description?><br />
						<strong>Type:</strong> <?=$address->type?><br />
						<?=$address->firstname." ".$address->lastname?><br />
						<?=$address->address?><br />
						<?php if($address->address_2): ?>
						<?=$address->address_2?><br />
						<?php endif ?>
						<?=$address->city?>, <?=$address->state?>, <?=$address->zip?><br />
						<?=$this->html->link('Edit', array('controller' => 'Addresses', 'action' => 'edit', 'args' => $address->_id)); ?>
					| <a href="#" id="remove_<?=$address->_id?>" title="Remove Address" class="address_remove">Remove</a>
					<hr/>
				</div>	
			<?php endforeach ?>
		<?php else : ?>
		<div style="text-align:center;">You don't have any addresses yet.</div>
		<?php endif ?>
<div class="clear"></div>		
<?php if (!empty($removeButtons)): ?>
	<?php foreach ($removeButtons as $button): ?>
		<?php //echo $button ?>
	<?php endforeach ?>
<?php endif ?>

<script type="text/javascript">

	$(document).ready( function() {
		$(".address_remove").each( function() {	
		
			var address_id = this.id.replace("remove_", "");
		
			$('#' + this.id + "").click ( function () {
			
				var remove = confirm ("Are you sure you want to remove this address?"); 
				
				if ( remove ) {					
			    	$('#' + address_id + "").remove();
			    	$.ajax({ url: $.base + "addresses/remove", 
			    			 data: address_id, 
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