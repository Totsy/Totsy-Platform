<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('table');?>

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
                    <td><?php echo $entry['date_created']?></td>
                    <td><?php echo $entry['reason']?></td>
                    <td><?php echo $entry['comment']?></td>
                     <td><?php echo $entry['created_by']?></td>
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