<?php

$fields = array(
	'firstName', 'lastName', 'company', 'address', 'city', 'state', 'zip', 'country'
);

foreach ($fields as $field) {
	if ($value = $address->{$field}) {
		echo "<{$field}>{$value}</{$field}>";
	}
}

foreach (array('phone', 'fax') as $field) {
	if ($value = $address->{$field}) {
		echo "<{$field}Number>{$value}</{$field}Number>";
	}
}

?>
