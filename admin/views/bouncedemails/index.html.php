<?=use lithium\storage\cache\strategy\Base64;
$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>

<div class="grid_16">
	<h2 id="page-heading">Select Event for <?=$type?> Administration
	<?php if ($environment == 'local'): ?>
		 - Dev Environment - Only Last 3 Months Events
	<?php endif ?>
	</h2>
</div>
<div class='clear'></div>
<div class='block' id="forms">
	<h5>Search for bounced email(s) by affiliate name OR starting from a start date OR starting from a end date.  You can
		Also click "Show todays" to show events starting today and future events.
	</h5>
	<fieldset>
	<?=$this->form->create(); ?>
		Affiliate Name:
			<?=$this->form->text('search' , array('id'=>'search')); ?>
			 &nbsp;&nbsp;&nbsp;
		Start date:
			<?=$this->form->text('start_date', array('id'=>'start_date','class'=>'date')); ?>
			&nbsp;
		End date:
			<?=$this->form->text('end_date', array('id'=>'end_date','class'=>'date') ); ?>
			&nbsp;&nbsp;
		Show Todays:
			<?=$this->form->checkbox('todays',array('value' => '1', 'id' => 'todays_checkbox'))?>
			&nbsp;&nbsp;
		Bounce: 
			<?=$this->form->select('bounce', array(
				'hard' => 'Hard',
				'soft' => 'Soft',
				'all' => 'ALL'
				), array('style' => 'width:100px; margin: 0px 20px 0px 0px;'));
			?>
	   <?=$this->form->submit('Find', array('class' => 'float-right')); ?>
	<?=$this->form->end(); ?>
	</fieldset>
</div>
<br/>
<?php if(!empty($retval)) :?>
	<div class="grid_16">
		<table id="affiliateTable" class="datatable" border="1">
			<thead>
				<tr>
					<td>Affiliate</td>
					<td># of Emails</td>
					<td>Export</td>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($retval as $val){ ?>
				<?php 
					$query = $request+array('invited_by'=>$val['invited_by']);
					ksort($query);
					$query['signature'] = md5(implode('',$query).$key); 
					$query = base64_encode(http_build_query($query));
				?>
				<?php $hash = md5($key.$val['invited_by'].$val['total']); ?>
				<tr>
					<td><a id="<?=$query ?>|<?=$hash?>" href="#" class="affiliates_mail"><?=$val['invited_by']?><a></td>
					<td><?=$val['total']?></td>
					<td><?=$this->form->checkbox($val['invited_by'],array('value' => '1'))?>
				</tr>
				<tr>
					<td id="td_<?=$hash?>" colspan="3" style="display:none;"></td>
				</tr>		
			<?php }?>
			</tbody>
		</table>
	</div>
<?php endif ?>
<div class='clear'></div>

<script type="text/javascript">
jQuery(function($){
 	$(".date").mask("99/99/9999");
 	$(".affiliates_mail").click(function(){
		var codes = $(this).attr('id').split('|'); 
		var id = codes[1];
		$.post('/bouncedemails/details', {'data': codes[0]}, function(data){
			if (data.status.code == 0){
				$('#td_'+id).html(data.response);		
			}
		}, true);
 	 	$('.affiliates_raw').hide().removeClass('affiliates_raw');
 	 	$('#td_'+id).show().addClass('affiliates_raw');
		return false;
 	});
});
</script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
	function filter() {
		$('#monthform').submit();
	};

	$('#todays_checkbox').change(function(){
		if ($('#todays_checkbox:checked').val() == '1'){
			$('#end_date').attr('disabled', 'disabled');
            $('#start_date').attr('disabled', 'disabled');

        }else{
            $('#end_date').removeAttr('disabled');
            $('#start_date').removeAttr('disabled');
        }
	});
</script>