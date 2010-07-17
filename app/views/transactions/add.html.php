<?php

$now = intval(date('Y'));
$years = array_combine(range($now, $now + 15), range($now, $now + 15));
$this->form->config(array('text' => array('class' => 'inputbox')));

?>
<style type="text/css">
	.shipping .form-row input[type=checkbox] {
		float: left;
		clear: right;
		margin-top: 10px;
	}
	.shipping .form-row label {
		margin-left: 4px;
	}
	.shipping .product-list {
		margin-top: 10px;
		border: 1px solid #CCCCCC;
		padding: 5px 0 5px 20px;
	}
	.shipping .product-list .form-row label {
		width: 200px;
	}
	form .submit {
		clear: left;
		float: left;
		width: 39%;
		margin-top: 95px;
	}
	form .addresses {
		width: 55%;
		float: right;
		clear: right;
	}
	form .payment {
		width: 39%;
		float: left;
		clear: left;
	}
</style>

<div id="page">
<?=$this->form->create($order); ?>
	<fieldset class="addresses">
		<h2 class="gray mar-b">Billing info</h2>
		<div class="form-row">
			<?=$this->form->field('billing_address', array(
				'type' => 'select', 'list' => $addresses
			)); ?>
		</div>

		<br />

		<h2 class="gray mar-b">Choose a destination for each item</h2>
		<div class="shipping">
			<?php if (count($cart) > 3) { ?>
			<div>
				<?=$this->form->checkbox('select_all', array('id' => 'select_all')); ?>
				<?=$this->form->label('select_all', 'Select all'); ?>
				<?=$this->form->select('shipping_all', $addresses, array('id' => 'shipping_all')); ?>
			</div>
			<?php } ?>
			<div class="product-list">
				<?php foreach ($cart as $item) { ?>
					<div class="form-row">
						<?=$this->form->checkbox("select_{$item->_id}"); ?>
						<?=$this->form->label("shipping_{$item->_id}", $item->description); ?>
						<?=$this->form->select("shipping[{$item->_id}]", $addresses, array(
							'id' => "shipping_{$item->_id}"
						)); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</fieldset>

	<fieldset class="payment">
		<h2 class="gray mar-b">Payment information</h2>
		<?=$this->form->field('card[type]', array(
			'label' => 'Card Type', 'type' => 'select', 'wrap' => array('class' => 'form-row'), 'list' => array(
				'visa' => 'Visa',
				'mc' => 'MasterCard',
				'amex' => 'American Express'
			)
		)); ?>
		<?=$this->form->field('card[name]', array(
			'label' => 'Name on Card', 'wrap' => array('class' => 'form-row')
		)); ?>
		<?=$this->form->field('card[number]', array(
			'label' => 'Card Number', 'wrap' => array('class' => 'form-row')
		)); ?>
		<div class="form-row">
			<?=$this->form->label('Expires'); ?>
			<?=$this->form->select('card[month]', array(
				1 => 'January',
				2 => 'February',
				3 => 'March',
				4 => 'April',
				5 => 'May',
				6 => 'June',
				7 => 'July',
				8 => 'August',
				9 => 'September',
				10 => 'October',
				11 => 'November',
				12 => 'December'
			)); ?>
			<?=$this->form->select('card[year]', $years); ?>
		</div>
		<?=$this->form->field('card[code]', array(
			'label' => 'Card Code', 'wrap' => array('class' => 'form-row')
		)); ?>
		<?=$this->form->field('save_card', array(
			'type' => 'checkbox',
			'id' => 'save_card',
			'label' => 'Save my card information for later',
			'template' => 'field-checkbox'
		)); ?>
		<div id="save_card_info" class="form-row">
			<?=$this->form->label('card_name', 'Give this card a name'); ?>
			<?=$this->form->text('card_name'); ?>
		</div>
	</fieldset>
	<div class="submit">
		<?=$this->form->submit('Checkout', array('class' => 'flex-btn')); ?>
	</div>
<?=$this->form->end(); ?>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('input[name=save_card]').bind('click', function() {
		$(this).attr('checked') ? $('#save_card_info').show() : $('#save_card_info').hide();
	});
	$('#select_all').bind('click', function() {
		$('.product-list input[type=checkbox]').attr('checked', $(this).attr('checked'));
	});
	$('#shipping_all').bind('change', function() {
		value = $(this).val();
		$('.product-list input[type=checkbox]:checked').each(function() {
			$(this).parent().find('select').val(value);
		});
	});
	$('#save_card_info').hide().css({ position: 'absolute' });
});
</script>