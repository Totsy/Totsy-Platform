<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Admin User Management</h2>
</div>
<div id="clear"></div>

<div class="grid_16">
<?php if (!empty($admin['superadmin']) && $admin['superadmin'] == true): ?>
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
            <?php foreach($admins as $user): ?>
                <tr>
                    <?php if (array_key_exists('created_date', $user)): ?>

                        <?php if (is_string($user['created_date'])): ?>
                            <td><?php echo date('m/d/Y', strtotime($user['created_date']));?></td>
                        <?php else:?>
                            <td><?php echo date('m/d/Y', $user['created_date']->sec);?></td>
                        <?php endif;?>

                    <?php else:?>

                        <?php if (is_string($user['created_orig'])): ?>
                            <td><?php echo date('m/d/Y', strtotime($user['created_orig']));?></td>
                        <?php else:?>
                            <td><?php echo date('m/d/Y', $user['created_orig']->sec);?></td>
                        <?php endif;?>

                    <?php endif;?>
                    <td><?php echo $user['email'];?></td>
                     <?php if (array_key_exists('firstname', $admin)): ?>
                        <td><?php echo $user['firstname'];?></td>
                    <?php else:?>
                        <td><?php echo ""?></td>
                    <?php endif;?>

                    <?php if (array_key_exists('lastname', $user)): ?>
                       <td><?php echo $user['lastname'];?></td>
                    <?php else:?>
                        <td><?php echo ""?></td>
                    <?php endif;?>

                    <?php if (array_key_exists('admin', $user) && $user['admin']): ?>
                       <td>Access</td>
                    <?php else:?>
                       <td>No Access</td>
                    <?php endif;?>

                    <?php if (array_key_exists('admin', $user) && $user['admin']): ?>
                       <td><input type="button" name="Deny" value="Deny" onclick="changeAccess('<?php echo $user[email]?>','deny','admin')"/></td>
                    <?php else:?>
                       <td><input type="button" name="Allow" value="Allow" onclick="changeAccess('<?php echo $user[email]?>','allow','admin')"/></td>
                    <?php endif;?>

                    <?php if (array_key_exists('superadmin', $user) && $user['superadmin']): ?>
                       <td><input type="button" name="Deny" value="Deny"onclick="changeAccess('<?php echo $user[email]?>','deny','superadmin')"/></td>
                    <?php else:?>
                       <td><input type="button" name="Allow" value="Allow" onclick="changeAccess('<?php echo $user[email]?>','allow','superadmin')"/></td>
                    <?php endif;?>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
 <?php else:?>
    You do not have any authorization to view this page.
<?php endif;?>
</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#admin_table').dataTable();
	} );
</script>
<script type="text/javascript" charset="utf-8">
	function changeAccess(email,access,level) {
	    $.post('/users/adminManager', {email:email,access:access,type:level},function(){
	        location.reload();
	    });
	}
</script>
