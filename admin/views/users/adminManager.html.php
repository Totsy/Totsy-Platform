<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Admin User Management</h2>
</div>
<div id="clear"></div>

<div class="grid_16">
<table id="admin_table">
    <thead>
        <th> Registered Date </th>
        <th> Email </th>
        <th> First Name </th>
        <th> Last Name </th>
        <th> Admin Access Status </th>
        <th>Grant/Remove Admin Access</th>
        <th>Grant/Remove Super-Admin Access</th>
    </thead>
    <tbody>
        <?php foreach($admins as $admin): ?>
            <tr>
                <?php if (array_key_exists('created_date', $admin)): ?>
                    <td><?php echo date('m/d/Y', $admin['created_date']->sec);?></td>
                <?php else:?>
                    <td><?php echo date('m/d/Y', $admin['created_orig']->sec);?></td>
                <?php endif;?>
                <td><?php echo $admin['email'];?></td>
                 <?php if (array_key_exists('firstname', $admin)): ?>
                    <td><?php echo $admin['firstname'];?></td>
                <?php else:?>
                    <td><?php echo ""?></td>
                <?php endif;?>
                <?php if (array_key_exists('lastname', $admin)): ?>
                   <td><?php echo $admin['lastname'];?></td>
                <?php else:?>
                    <td><?php echo ""?></td>
                <?php endif;?>
                <?php if (array_key_exists('admin', $admin) && $admin['admin']): ?>
                   <td>Access</td>
                <?php else:?>
                   <td>No Access</td>
                <?php endif;?>
                <?php if (array_key_exists('admin', $admin) && $admin['admin']): ?>
                   <td><input type="button" name="Deny" value="Deny" onclick="changeAccess('<?php echo $admin[_id]?>','deny','admin')"></td>
                <?php else:?>
                   <td><input type="button" name="Allow" value="Allow" onclick="changeAccess('<?php echo $admin[_id]?>','allow','admin')"></td>
                <?php endif;?>
                <?php if (array_key_exists('superadmin', $admin) && $admin['superadmin']): ?>
                   <td><input type="button" name="Deny" value="Deny"onclick="changeAccess('<?php echo $admin[_id]?>','deny','admin')"></td>
                <?php else:?>
                   <td><input type="button" name="Allow" value="Allow" onclick="changeAccess('<?php echo $admin[_id]?>','allow','admin')"></td>
                <?php else:?>
                   <td><input type="button" name="Allow" value="Allow"onclick="changeAccess('<?php echo $admin[_id]?>','deny','superadmin')"></td>
                <?php else:?>
                   <td><input type="button" name="Allow" value="Allow" onclick="changeAccess('<?php echo $admin[_id]?>','allow','superadmin')"></td>
                <?php endif;?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#admin_table').dataTable();
	} );
</script>
<script type="text/javascript" charset="utf-8">
	function changeAccess(userid,access,level) {

	}
</script>
