<br>
<br>
<br>
<br>
<?php foreach($promocodes as $promocode): ?>
    Code: <?=$promocode->code; ?> <br>
    Enabled: <?=$promocode->enabled; ?> <br>
    Code type: <?=$promocode->type; ?><br>
    <?php if($promocode->type == 'percent'): ?>
        Discount Amount: <?=$promocode->discount_amount; ?><br>
    <?php endif; ?>
    <?php if($promocode->type == 'dollar'): ?>
        Discount Amount: $<?=$promocode->discount_amount; ?><br>
    <?php endif; ?>
        Created on: <?=$promocode->date_created; ?><br>
    Start Date: <?=$promocode->start_date; ?><br>
    Expiration Date: <?=$promocode->end_date; ?><br>
    Min. Purchase: $<?=$promocode->minimum_purchase; ?><br>
    <?=$this->html->link('edit', 'promocodes/edit/'.$promocode->_id); ?>
    <br>
    <br>
   
<?php endforeach; ?>

