<script type="text/javascript">
var cc_info = new Object();


</script>


<?=$this->form->create($orderPromo,array('id' => 'promo-form')); ?>
    <?php if (is_array($this->form->error('promo'))): ?>
        <?php foreach($this->form->error('promo') as $msg) :?>
            <?php echo $msg ?>
        <?php endforeach; ?>
    <?php else: ?>
        <?=$this->form->error('promo'); ?>
    <?php endif; ?>
    <?=$this->form->text('code', array('size' => 6)); ?>
    <div id='promobtn'>
    	<?=$this->form->submit('Apply Promo Code'); ?>
    </div>
<?=$this->form->end(); ?>


<script type="text/javascript">
$(document).ready(function(){
    $('#promo-form').submit(function(){
        var data = {
        	code : $('input[name="code"]').val(),
        	'card[type]' : $('#card_type option:selected').text(),
        	'card[number]' : $('card[number]').val(),
        	'card[code]' : $('input[name="card[code]"]').val(),
        	'card[month]' : $('#card_month option:selected').text(),
        	'card[year]' : $('#card_year option:selected').text()
        };
      console.log(cc_info);
 

     $.ajax({
            url: "/orders/process",
            type: 'POST',
            data : 'test',
            error: function(data, textStatus, errorThrown){
            	console.log(errorThrown;
            	//alert(cc_info.cardmonth);
            	//$('#card_month option:selected').text(cc_info.cardmonth);
            }
        });
    });
    
   $('#promobtn').click(function(){
		cc_info.promocode = $('input[name="code"]').val(),
		cc_info.cardtype = $('#card_type option:selected').text();
		cc_info.cardnum = $('card[number]').val();
		cc_info.cardcode = $('input[name="card[code]"]').val();
		cc_info.cardmonth = $('#card_month option:selected').text();
		cc_info.cardyear = $('#card_year option:selected').text();
	});
    
});


</script>