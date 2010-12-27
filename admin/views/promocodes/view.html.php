<br>
<br>
<br>


<?php foreach( $promocodes as $promocode ): ?>
        <?php foreach( $promocode as $key => $value): ?>
            <?=$key; ?> : <?=$value; ?> <br>
        <?php endforeach; ?>
<?php endforeach; ?>

<?=$this->html->link('back', $_SERVER['HTTP_REFERER'] ); ?>