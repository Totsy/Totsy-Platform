<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('table');?>

<div id="status_history">
    <div class="box">
        <h2 >Deactivation History</h2>
        <div class="bock">
            <table id="historyTable" class="datatable" border="1">
                <thead>
                    <th>Deactivation Date</th>
                    <th>Reason</th>
                    <th>Comment</th>
                    <th>Deactivated By</th>
                </thead>
                <tbody>
                <?php
                    foreach($history as $entry):
                ?>
                <tr>
                    <td><?=$entry['date_created']?></td>
                    <td><?=$entry['reason']?></td>
                    <td><?=$entry['comment']?></td>
                     <td><?=$entry['created_by']?></td>
                </tr>
                <?php
                    endforeach;
                ?>
                <tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#historyTable').dataTable();
	} );
</script>