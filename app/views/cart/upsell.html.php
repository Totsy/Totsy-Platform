<img style="float:left" src="/img/Disneycover.jpg" width="150"/>
<div class="disney" id="left">
   <p style="font-size:14px"> Spend <strong style="color:#5CBC5C"> $<?=number_format($total_left,2);?> </strong>more and receive a years subscription to Disney Family Fun.</p>
</div>
<div class="clear"></div><br/>
<?=$this->html->link('Continue Shopping', "/sales", array('class' => 'button', 'style' => 'margin-right:10px;')); ?>