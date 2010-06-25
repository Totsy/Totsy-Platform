<?php 

$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
$leftMenu = $this->MenuList->build($menu, $options);
echo $leftMenu;

if($addressList){ ?>
	<table border="0" cellspacing="5" cellpadding="5">
		<tr>
			<th>Description</th>
			<th>Address</th>
			<th>Default</th></tr>
		<tr>
<?php	foreach($addressList as $addresses) {
				$line = implode(', ', array($addresses['city'],$addresses['state'],$addresses['zip']));	
?>
		<td><?=$addresses['description'];?></td>
		<td>
			<?php echo "<a href=/addresses/edit/". ($addresses['_id']); ?>			
			<p><?=$addresses['firstname']." ".$addresses['lastname'] ?></p>
			<p><?=$addresses['company']?></p>
			<p><?=$addresses['address']?></p>
			<p><?=$addresses['address_2']?></p>
			<p><?=$line?></p>
			<p><?=$addresses['country']?></p>	
			</a>	
		</td>
		<td><?php if($addresses['default'] == 'Yes') {
			echo "Default - $addresses[type]";
			};?>
		</td>
	</tr>
	    
	<?php  } ?>
</table>	
<?php } else {
	
		echo "No Addresses on file. ";
}
?>
<?=$this->html->link('Add Address','addresses/add');?>



