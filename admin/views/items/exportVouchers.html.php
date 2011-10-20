<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Items - Vouchers Reports</h2>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php if (!empty($datas)): ?>
		<table id="summary_table" class="datatable" border="1">
			<thead>
				<tr>
					<th>Voucher</th>
					<th>Email Address</th>
					<th>order_id</th>
				</tr>
			</thead>
			<?php foreach($datas as $data) : ?>
			<tr>
				<td><?=$data['voucher']?></td>
				<td><?=$data['email'];?></td>
				<td><?=$data['order_id'];?></td>
			</tr>
			<?php endforeach ?>
		</table>
	<?php endif ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#summary_table').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": false,
			"bFilter": false
		}
		);
	} );
</script>