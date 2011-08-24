<?=$this->form->create($orderPromo,array('id' => 'promo-form')); ?>
    <div style='float:right !important; width:200px !important; margin-left: 10px; text-align:center'>
    <?php if (is_array($this->form->error('promo'))): ?>
        <?php foreach($this->form->error('promo') as $msg) :?>
            <?php echo $msg ?>
        <?php endforeach; ?>
    <?php else: ?>
        <?=$this->form->error('promo');?>
    <?php endif; ?>
    </div>
    <?php if($this->form->error('promo')) { ?>
    	<script type="text/javascript">discountErrors.promo = true;</script>
    <?php } ?>
    <input type="text" name="code" style='width:70px;' />
    <span id='promobtn'>
    	<?=$this->form->submit('Apply Promo Code'); ?>
    </span>
<?=$this->form->end(); ?>