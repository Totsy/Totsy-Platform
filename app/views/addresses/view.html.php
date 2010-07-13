<?php 

$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
$leftMenu = $this->MenuList->build($menu, $options);
echo $leftMenu;

if($addresses){ ?>
	<table border="0" cellspacing="5" cellpadding="5">
		<tr>
			<th>Description</th>
			<th>Address</th>
			<th>Default</th></tr>
		<tr>
<?php	foreach($addresses as $address) {
				$line = implode(', ', array($address['city'],$address['state'],$address['zip']));	
?>
		<td><?=$address['description'];?></td>
		<td>
			<?php echo "<a href=/addresses/edit/". ($address['_id']); ?>
			<p><?=$address['firstname']." ".$address['lastname'] ?></p>
			<p><?=$address['company']?></p>
			<p><?=$address['address']?></p>
			<p><?=$address['address_2']?></p>
			<p><?=$line?></p>
			<p><?=$address['country']?></p>	
			</a>	
		</td>
		<td><?php if($address['default'] == 'Yes') {
			echo "Default - $address[type]";
			};?>
		</td>
	</tr>
	    
	<?php  } ?>
</table>	
<?php } else {
	
		echo "No address on file. ";
}
?>
<?=$this->html->link('Add Address','addresses/add');?>



