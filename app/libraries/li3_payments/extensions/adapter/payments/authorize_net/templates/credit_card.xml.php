<creditCard>
	<?php if ($card->number) { ?>
		<cardNumber><?=$card->number; ?></cardNumber>
	<?php } ?>
	<?php if ($card->year && $card->month) { ?>
		<expirationDate><?=$card->year; ?>-<?=$card->month; ?></expirationDate>
	<?php } ?>
	<?php if ($card->code) { ?>
		<cardCode><?=$card->code; ?></cardCode>
	<?php } ?>
</creditCard>
