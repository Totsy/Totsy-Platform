<?php if ( !empty($message) ): ?>
    
   <h4><?=$message ?> </h4>
    
<?php endif; ?>

<?=$this->form->create(); ?>
    Enable: <?=$this->form->checkbox('enabled', array('checked'=>'checked', 'value' => '1')); ?> <br>
    <br>
    Enter code:
    <?=$this->form->text('code', array('value' => 'Enter code')); ?><br>
    <br>
    Enter description:
    <?=$this->form->textarea('description', array('value' => 'Enter description here')); ?><br><br>
    Code Type:
   <?=$this->form->select( 'type', array('percent' => 'percent',  'dollar'=> 'dollar amount', 'shipping'=> 'shipping') ); ?><br><br>
   Enter discount amount:
   <?=$this->form->text( 'discount_amount', array( 'value' => 'Enter discount amount here') ); ?><br><br>
   Enter minimum:
   <?=$this->form->text( 'minimum_purchase', array( 'value' => 'Enter minimum purchase') ); ?><br><br>
   Enter max:
   <?=$this->form->text( 'max_use', array( 'value' => 'Enter max use') ); ?><br><br>
   Enter start date:
   <?=$this->form->text( 'start_date', array('value' => 'Enter start date here') ); ?><br><br>
   Enter end date:
   <?=$this->form->text( 'end_date', array('value' => 'Enter end date here') ); ?><br><br>
   <?=$this->form->submit('create'); ?><br><br>
 
<?=$this->form->end(); ?>