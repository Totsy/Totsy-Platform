<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->style('TableTools');?>

<style type="text/css">
	td {
		font-size:12px;
		text-align: center;
	}
	th {
		font-size:12px;
		text-align: center;
	}
</style>

<div class="grid_16">
	<h2 id="page-heading">Promocode Revenue</h2>
	<?php echo $this->html->link('Daily Revenue', 'Revenue::daily')."&nbsp;&nbsp;"; ?>
	<?php echo $this->html->link('Monthly Revenue', 'Revenue::monthly')."<br/>"; ?>
</div>
<div class="clear"></div>

<div class="clear"></div>
<div class="grid_16">
	<table id="promocode_table" class="" border="1">
		<thead>
			<tr>
				<th>Month</th>
				<th>Code</th>
				<th>Amount Saved</th>
				<th>Code Value</th>
				<th>Code Type</th>
				<th>Number Used</th>
				<th>Net Revenue</th>
				<th>Gross Revenue</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($promocodes as $month => $promocode_month) {
				foreach ($promocode_month as $promocode) {
					if ( $promocode['code'] == 'Total' ) {
						echo '<tr class="parent" id="'.$month.'"><td><b>'.$month.'</b></td>';
						$promocode['code'] = '';
					}
					else
						echo '<tr class="child-'.$month.'"><td></td>';
					echo '<td>'.$promocode['code'].'</td>';
					echo '<td>'.number_format($promocode['amount_saved'],2, '.', '').'</td>';
					echo '<td>'.$promocode['value'].'</td>';
					echo '<td>'.$promocode['type'].'</td>';
					echo '<td>'.$promocode['number_used'].'</td>';
					echo '<td>'.number_format($promocode['net'],2, '.', '').'</td>';
					echo '<td>'.number_format($promocode['gross'],2, '.', '').'</td></tr>';
				}
			}
			
			?>
		</tbody>
	</table>
</div>

<script type="text/javascript" charset="utf-8">
	
	$(document).ready(function() {
	
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#promocode_table').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": false,
			"bSort" : false,
			"bFilter": false
		});
		
		$('tr.parent')
		.css("cursor","pointer")
		.attr("title","Click to expand/collapse")
		.click(function(){
			$(this).siblings('.child-'+this.id).toggle();
		
	} );
	
	$('tr[class^="child-"]').hide().children('td');
});
	
	
</script>