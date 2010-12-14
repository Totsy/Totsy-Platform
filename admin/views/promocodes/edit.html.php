
<?=$this->form->create(); ?>
    <?php if ( $promocode->enabled ): ?>
        Enable: <?=$this->form->checkbox( 'enabled', array( 'checked'=>'checked', 'value' => '1' ) ); ?> <br>
    <?php else: ?>
        Enable: <?=$this->form->checkbox( 'enabled', array( 'value' => '0' ) ); ?> <br>
    <?php endif; ?>
    
   Code: <?=$this->form->text('code', array( 'value' => $promocode->code ) ); ?><br>
  Description: 
  <?=$this->form->textarea('description', array( 'value' => $promocode->description ) ); ?><br>
  Code Type:
   <?=$this->form->select('type', array('percent' => 'percent',  'dollar'=> 'dollar amount', 'shipping'=> 'shipping'), array('id' => 'type' , 'value' => $promocode->type) ); ?><br>
    Discount Amount:
   <?=$this->form->text('discount_amount', array( 'value' => $promocode->discount_amount)); ?><br>
   Minimum Purchase:
   <?=$this->form->text('minimum_purchase', array( 'value' => $promocode->minimum_purchase)); ?><br>
    Enter max:
   <?=$this->form->text( 'max_use', array( 'value' => $promocode->max_use) ); ?><br><br>   
   Start Date:
   <?=$this->form->text('start_date', array('value' => $promocode->start_date)); ?><br>
   Expiration Date:
   <?=$this->form->text('end_date', array('value' => $promocode->end_date)); ?><br>
      
   <?=$this->form->submit('update'); ?>
<?=$this->form->end(); ?>