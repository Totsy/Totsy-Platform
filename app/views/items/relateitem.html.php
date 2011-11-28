<?php

$all_items = "";

$item_dropdown .= "<select multiple size=7 id='all_items'>";

foreach ($itemRecords as $item) {
	$item_dropdown .= "<option value='$item->_id'>".$item->color." - ".$item->description."</option>";
}

$item_dropdown .= "</select>";

return $item_dropdown;

?>
