<br>
<br>
<br>

<?php foreach( $promotions as $promotion ): ?>
    
    Promocode: <?=$this->html->link($promotion->code, 'promocodes/view/'.$promotion->code ); ?> <br>
    Userid: <?=$promotion->user_id; ?><br>
    Amount Saved: $<?=$promotion->saved_amount; ?><br>
    Created on: <?=$promotion->date_created; ?><br>
    <br>
    <br>

<?php endforeach; ?>