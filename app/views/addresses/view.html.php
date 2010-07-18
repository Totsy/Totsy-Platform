<?php 
	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	$leftMenu = $this->MenuList->build($menu, $options);
	echo $leftMenu;
?>
<?=$this->html->link('Add Address','Addresses::add');?>
<table width="70%" class="cart-table">
	<thead>
		<tr>
			<th>
				#
			</th>
			<th>
				Address Type
			</th>
			<th>
				Description
			</th>
			<th>
				Address
			</th>
			<th>
				Remove Address
			</th>
		</tr>
	</thead>
	<tbody>
	<?php $x = 0?>
	<?php foreach ($addresses as $address): ?>
		<?php $x++; ?>
		<tr id="<?=$address->_id?>">
		<td>
			<?=$x?>
		</td>
		<td>
			<?=$address->type?>
		</td>
		<td>
			<?=$address->description?>
		</td>
		<td>
			<div id='name'><?=$address->firstname." ".$address->lastname?></div>
			<div id='address'>
				<?=$address->address?><br><?=$address->address_2?><br>
				<?=$address->city?>, <?=$address->state?>, <?=$address->zip?><br>
				<?=$address->country?><br>
				<?=$this->html->link('Edit', "addresses/edit/$address->_id"); ?>
			</div>
		</td>
		<td align='center'>
			<a href="#" id="<?php echo "remove$address->_id"?>" title="Remove Address" class="delete">delete</a>
		</td>
		<?php
			$removeButtons[] = "<script type=\"text/javascript\" charset=\"utf-8\">
					$('#remove$address->_id').click(function () { 
						$('#$address->_id').remove();
						$.ajax({url: \"addresses/remove\", data:'$address->_id', context: document.body, success: function(data){
						      }});
					    });
				</script>";
		?>
	<?php endforeach ?>
		</tr>
	</tbody>
</table>

<?php if (!empty($removeButtons)): ?>
	<?php foreach ($removeButtons as $button): ?>
		<?php echo $button ?>
	<?php endforeach ?>
<?php endif ?>




