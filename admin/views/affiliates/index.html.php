<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('table');?>
<?=$this->html->style('TableTools');?>

<div class="grid_16">
	<h2 id="page-heading"> Affiliates</h2>
</div>

<div class='grid_3 menu'>
	<table>
		<thead>
			<tr>
				<th>Affiliate Navigation </th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td> <?php echo $this->html->link('Create Affiliate', 'affiliates/add'); ?> </td>
			</tr>
			<tr>
				<td><?php echo $this->html->link('View/Edit Affiliate', 'affiliates/index' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>

<div class='grid_16 box'>

    <table id='codeSummary' class='datatable' >
        <thead>
            <tr>
                <th> Affiliates Name </th>
                <th> Affiliates Level </th>
                <th> Active Affiliate </th>
                <th> Pixels? </th>
                <th> Created By: </th>
                <th> Created Date </th>
                 <th>   </th>
            </tr>
        </thead>
         <tbody>
            <?php foreach($affiliates as $affiliate): ?>
				<tr>
					<td> <?php if(!empty($affiliate['name'])) echo $affiliate['name']; ?> </td>
					<td> <?php if(!empty($affiliate['level'])) echo $affiliate['level']; ?> </td>
					<td> <?php if(!empty($affiliate['active'])) echo $affiliate['active']; ?> </td>
					<td> <?php if(!empty($affiliate['active_pixel'])) echo $affiliate['active_pixel']; ?> </td>
					<td> <?php if(!empty($affiliate['created_by'])) echo $affiliate['created_by']; ?> </td>
					<td> <?php if(!empty($affiliate['date_created'])) echo $affiliate['date_created']; ?> </td>
					<td> <?=$this->html->link('edit', 'affiliates/edit/'.$affiliate['_id']); ?> </td>
				</tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#codeSummary').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false
		}
		);
	} );
</script>
