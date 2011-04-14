<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>


<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#min_date, #max_date').datetimepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == "min_date" ? "minDate" : "maxDate";
				var instance = $(this).data("datetimepicker");
				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
			}
		});
	});
</script>
<div class="grid_6">
	<div class="box">
	<h2>
		<a href="#" id="toggle-forms">Query for Affiliate Order/Count Totals</a>
	</h2>
	<div class="block" id="forms">
		<fieldset>
			<?=$this->form->create($search); ?>
				<p>
					<?=$this->form->label('Affiliate'); ?>
					<?=$this->form->text('affiliate'); ?>
				</p>
				    <?php
				        if(($criteria) && (bool)$criteria['subaffiliate']){
				            $checked = 'checked';
				        }else{
				            $checked = '';
				        }
				    ?>
				    <?=$this->form->label('Subaffiliates included'); ?>  <?=$this->form->checkbox('subaffiliate', array('checked' => $checked, 'value' => '1'));?> <br/>
				<p>
					<?=$this->form->label('Minimum Seach Date'); ?>
					<?=$this->form->text('min_date', array('id' => 'min_date'));?>
				</p>
				<p>
				<?=$this->form->label('Maximum Seach Date'); ?>
				<?=$this->form->text('max_date', array('id' => 'max_date'));?>
				</p>
				<p>
					<?=$this->form->label('Search Type'); ?>
					<?=$this->form->select('search_type', array(
						'Revenue' => 'Total Revenue',
						'Registrations' => 'Total Registrations'
						));
					?>
				</p>
				<?=$this->form->submit('Search'); ?>
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>
<div class="clear"></div>
<?php if (!empty($results)): ?>
	<div class="grid_16">
			<table id="report" class="datatable" border="1">
				<thead>
					<tr>
						<th>Month/Year</th>
						<th>Total - <?=$searchType?></th>
					</tr>
				</thead>
				<tbody>
				    <?php
				        if(($criteria) && (bool)$criteria['subaffiliate']):
				            $reports = array();
				            foreach ($results['retval'] as $result){
				                $reports[$result['Date']][] = $result;
				            }
				            $results['retval'] = $reports;
				            foreach($results['retval'] as $month => $values):

				    ?>
				        <tr>
				            <td colspan = "2"><?=date('F',  mktime(0, 0, 0, ($month + 1)))?></td>
				        <tr>

				    <?php
				                foreach($values as $value):
				    ?>
				        <tr>
				                <td><?=$value['subaff']?></td>
                                <?php if ($searchType == 'Revenue'): ?>
                                    <td>$<?=number_format($value['total'], 2)?></td>
                                <?php else: ?>
                                    <td><?=$value['total']?></td>
                                <?php endif ?>
                        </tr>
					<?php
					            endforeach;
					        endforeach;
					?>

					<?php
					    else:
					    foreach ($results['retval'] as $result):
					?>
						<tr>
							<td><?=date('F/Y',  mktime(0, 0, 0, ($result['Date'] + 1),30,($result['Year'])))?></td>
							<?php if ($searchType == 'Revenue'): ?>
								<td>$<?=number_format($result['total'], 2)?></td>
							<?php else: ?>
								<td><?=$result['total']?></td>
							<?php endif ?>
						</tr>
					<?php
					        endforeach;
					    endif;
					?>

				</tbody>

				<?php if ($results['total'] != '$0' && $results['total'] != '0'): ?>
				<tfooter>
					<tr>
						<th>Grand Total<?php echo " - ".$searchType; ?> : </th>
						<th> <?php echo $results['total'] ?></th>
					</tr>
				</tfooter>
				<?php endif ?>
			</table>
	</div>
<?php endif ?>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#report').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false
		}
		);
	} );
</script>