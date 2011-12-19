<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>


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
			<?php echo $this->form->create($search); ?>
				<p>
					<?php echo $this->form->label('Affiliate'); ?>
					<?php echo $this->form->text('affiliate'); ?>
				</p>
				    <?php
				        if(($criteria) && (bool)$criteria['subaffiliate']){
				            $checked = 'checked';
				        }else{
				            $checked = '';
				        }
				    ?>
				    <?php echo $this->form->label('Subaffiliates included'); ?>  <?php echo $this->form->checkbox('subaffiliate', array('checked' => $checked, 'value' => '1'));?> <br/>
				<p>
					<?php echo $this->form->label('Minimum Seach Date'); ?>
					<?php echo $this->form->text('min_date', array('id' => 'min_date'));?>
				</p>
				<p>
				<?php echo $this->form->label('Maximum Seach Date'); ?>
				<?php echo $this->form->text('max_date', array('id' => 'max_date'));?>
				</p>
				<p>
					<?php echo $this->form->label('Search Type'); ?>
					<?php echo $this->form->select('search_type', array(
						'Revenue' => 'Total Revenue',
						'Registrations' => 'Total Registrations',
						'Bounces' => 'Total Bounces'
						));
					?>
				</p>
				<?php echo $this->form->submit('Search'); ?>
			<?php echo $this->form->end(); ?>
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
						<th>Total - <?php echo $searchType?></th>
						<?php if ($searchType == 'Registrations'): ?>
						<th>Total - Bounced</th>
						<?php endif; ?>
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
				            <td colspan = "2"><?php echo date('F',  mktime(0, 0, 0, ($month + 1)))?></td>
				        <tr>

				    <?php
				                foreach($values as $value):
				    ?>
				        <tr>
				                <td><?php echo $value['subaff']?></td>
                                <?php if ($searchType == 'Revenue'): ?>
                                    <td>$<?php echo number_format($value['total'], 2)?></td>
                                <?php else: ?>
                                    <td><?php echo $value['total']?></td>
                                <?php endif ?>
                                <?php if ($searchType == 'Registrations'): ?>
                                	<td><?php echo $value['bounced']?></td>
                                <?php endif; ?>
                                
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
							<td><?php echo date('F/Y',  mktime(0, 0, 0, ($result['Date'] + 1),30,($result['Year'])))?></td>
							<?php if ($searchType == 'Revenue'): ?>
								<td>$<?php echo number_format($result['total'], 2)?></td>
							<?php else: ?>
								<td><?php echo $result['total']?></td>
							<?php endif ?>
                            <?php if ($searchType == 'Registrations'): ?>
                               	<td><?php echo $result['bounced']?></td>
                            <?php endif; ?>

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
                    <?php if ($searchType == 'Registrations'): ?>
                        <th><?php echo $results['bounced']?></th>
                    <?php endif; ?>
					</tr>
				</tfooter>
				<?php endif ?>
			</table>
	</div>
<?php endif ?>

<?php if (!empty($cursor) && !empty($total)):?>
	<div class="grid_16">
		<table id="report" class="datatable" border="1">
			<thead>
				<tr>
					<th>Email</th>
					<th>Created Date</th>
					<th>Bounce Type</th>
					<th>Last Bounced</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($cursor as $row): ?>
				<tr>
					<td><?php echo $row['email'];?></td>
					<td><?php echo date('m/d/Y',$row['created_date']->sec);?></td>
					<td><?php echo $row['email_engagement']['type'];?></td>
					<td><?php echo date('m/d/Y',$row['email_engagement']['date']->sec);?></td>
				</tr>
			<?php endforeach;?>
			</tbody>
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