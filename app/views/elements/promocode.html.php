<?=$this->form->create($orderPromo,array('id' => 'promo-form')); ?>
    <?php if (is_array($this->form->error('promo'))): ?>
        <?php foreach($this->form->error('promo') as $msg) :?>
            <?php echo $msg ?>
        <?php endforeach; ?>
    <?php else: ?>
        <?=$this->form->error('promo'); ?>
    <?php endif; ?>
    <?=$this->form->text('code', array('size' => 6)); ?>
    <?=$this->form->submit('Apply Promo Code'); ?>
<?=$this->form->end(); ?>


<script type="text/javascript">
$().ready(function(){
    $('#promo-form').submit(function(){
        var code = $('input[name="code"]').val();
        var card['type'] = $('#card_type option:selected').val();
        var card['number'] = $('input[name="card[number]"]').val();
        var card['code'] = $('input[name="card[code]"]').val();
        var card['month'] = $('#card_month option:selected').val();
        var card['year'] = $('#card_year option:selected').val();
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