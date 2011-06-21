<?=$this->form->create($orderPromo,array('id' => 'promo-form')); ?>
    <?php if (is_array($this->form->error('promo'))): ?>
        <?php foreach($this->form->error('promo') as $msg) :?>
            <?php echo $msg ?>
        <?php endforeach; ?>
    <?php else: ?>
        <?=$this->form->error('promo'); ?>
    <?php endif; ?>
    <?=$this->form->text('code', array('size' => 6)); ?>
    <?=$this->form->submit('Apply Promo Code', array('class' => 'button')); ?>
<?=$this->form->end(); ?>


<script type="text/javascript">
$().ready(function(){
    $('#promo-form').submit(function(){
        var code = $('input[name="code"]').val();
        var card = $('input[name="card"]').val();
        $.ajax({
            url:"/orders/process",
            type:"post",
            cache:false,
            data:"code=" + code + "&card=" + card,
            success: function(data){

            }
        });
        return false;
    });
});
</script>