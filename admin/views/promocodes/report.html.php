<br>
<br>
<br>

<?=$this->form->create(); ?>
    <h5>Search code: </h5>
        <?=$this->form->text('search'); ?>
    <nbsp>
        <h5> Date Range: </h5> <nbsp>
        start:
        <?=$this->form->text('start'); ?> 
        end:
        <?=$this->form->text('end'); ?>
    
   <?=$this->form->submit('search'); ?><br><br>
<?=$this->form->end(); ?>

<br>
<?php foreach( $promotions as $promotion ): ?>
    
    Promocode: <?=$this->html->link($promotion->code, 'promocodes/view/'.$promotion->code ); ?> <br>
    Userid: <?=$this->html->link($promotion->user_id, 'users/view/'.$promotion->user_id, array('target' => '_blank')); ?><br>
    Order: <?=$this->html->link($promotion->order_id, 'orders/view/'.$promotion->order_id, array('target' => '_blank') );?><br>
    Amount Saved: $<?php echo round($promotion->saved_amount, 2 ); ?><br>
    Created on: <?=$promotion->date_created; ?><br>
    <br>
    <br>

<?php endforeach; ?>