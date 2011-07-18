<?=$this->form->create($orderPromo,array('id' => 'promo-form')); ?>
    <?php if (is_array($this->form->error('promo'))): ?>
        <?php foreach($this->form->error('promo') as $msg) :?>
            <?php echo $msg ?>
        <?php endforeach; ?>
    <?php else: ?>
        <?=$this->form->error('promo'); ?>
    <?php endif; ?>
    <?=$this->form->text('code', array('size' => 6)); ?>
    <span id='promobtn'>
    	<?=$this->form->submit('Apply Promo Code'); ?>
    </span>
<?=$this->form->end(); ?>