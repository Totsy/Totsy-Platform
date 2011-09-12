<?php $this->title("Address Book"); ?>

<div class="grid_16">
	<h2 class="page-title gray">Address Book</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>
<div class="grid_11 omega roundy grey_inside b_side">
	<h2 class="page-title gray">Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?=$this->html->link('Add New Address','Addresses::add'); ?></span></h2>
	<hr />
		<?php if (!empty($addresses)): ?> 
		<div class="grid_11">
			
			<?php $x = 0?>
			<?php foreach ($addresses as $address): ?>
				<?php $x++; ?>
				<div id="<?=$address->_id?>">
						<strong>Location:</strong> <?=$address->description?><br />
						<?=$address->firstname." ".$address->lastname?><br />
						<?=$address->address?><br />
						<?=$address->address_2?><br />
						<?=$address->city?>, <?=$address->state?>, <?=$address->zip?><br />
						<?=$this->html->link('Edit', array('controller' => 'Addresses', 'action' => 'edit', 'args' => $address->_id)); ?>
					| <a href="javascript:;" id="<?php echo "remove$address->_id"?>" title="Remove Address">Remove</a>
				<?php
					$removeButtons[] = "<script type=\"text/javascript\" charset=\"utf-8\">
							$('#remove$address->_id').click(function () { 
								$('#$address->_id').remove();
								$.ajax({url: $.base + \"addresses/remove\", data:'$address->_id', context: document.body, success: function(data){
								      }});
							    });
						</script>";
				?>
			<hr/>
			<?php endforeach ?>
			
				</div>
				
			
		</div>
		<?php else : ?>
		<div style="text-align:center;">You don't have any addresses yet. </div>
		<?php endif ?>
</div>
</div>
<div class="clear"></div>

<?php if (!empty($removeButtons)): ?>
	<?php foreach ($removeButtons as $button): ?>
		<?php echo $button ?>
	<?php endforeach ?>
<?php endif ?>