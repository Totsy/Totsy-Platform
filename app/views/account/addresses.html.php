<?php 

use app\extensions\helper\Menu;

$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
$leftMenu = Menu::build($navigation, $options);
echo $leftMenu;

if($addressList){ ?>
	<table border="1" cellspacing="5" cellpadding="5">
		<tr>
			<th>Description</th>
			<th>Address</th>
			<th>Default</th></tr>
		<tr>
<?php	foreach($addressList as $address): 
		$line = implode(', ', array($address['city'],$address['state'],$address['zip']));
?>
		<td><?=$address['description'];?></td>
		<td>
			<p><?=$address['firstname']." ".$address['lastname'] ?></p>
			<p><?=$address['company']?></p>
			<p><?=$address['address']?></p>
			<p><?=$address['address_2']?></p>
			<p><?=$line?></p>
			<p><?=$address['country']?></p>		
		</td>
		<td><?php if($address['default'] == 'Yes') {
			echo "Default - $address[type]";
			};?>
		</td>
	</tr>
	    
	<?php endforeach; ?>
</table>	
<?php } else {
	
		echo "No Addresses on file. Please add an address.";
}
?>
<?=$this->html->link('Add Address','account/add');?>



