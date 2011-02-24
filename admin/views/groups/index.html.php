<?=$this->html->script('jquery-1.4.2');?>
<div class='box'>
	<h1>Groups - ACL Management</h1>
	<div class="clear"></div>
	<?=$this->form->create(null ,array('id'=>'groupsFrom')); ?>
	<?=$this->form->select("select_group", $select_groups, array("style" => "width:150px;font-weight:bold;font-size:13px;", "onChange" => 'this.form.submit();', 'value' => $group["name"])); ?><br />
	<?php if(!empty($group)): ?>
		<br />
			<div id="table_wrap" class="container_16">
				<div id="title_row" class="grid_16" style="background:#f7f7f7; border-bottom:1px solid #ddd; margin:0px 0px 5px 0px;">
					<h2>Group: <?=$group["name"]?></h2>
				</div>
				<?php $n=0;?>
				<?php if (!empty($group["acls"])): ?>
					<?php foreach($group["acls"] as $acl):?>
						<div id="acl_<?=$n?>" class="grid_16" style="background:#f7f7f7; border-bottom:1px solid #ddd; margin:0px 0px 5px 0px;">
							<div id="info_row" class="grid_14 alpha" style="padding:5px;">
								<span style="font-size:24px;">Acl <?=$n?></span>
								<span style="margin:0px 20px 0px 20px;">Route <?=$this->form->text("acl_route_".$n, array('class' => 'inputbox', 'value' => $acl["route"], "id" => "acl_route_".$n )); ?></span>
								<span>Connection <?=$this->form->text("acl_connection_".$n, array('class' => 'inputbox', 'value' => $acl["connection"], "id" => "acl_connection_".$n )); ?></span>
							</div>	
						 	<div id="cross_row"  class="grid_1 alpha" style="margin:5px 0px; padding:8px; text-align:right;"> 
						   		<a href="#" id="cancel_button" onclick="cancel('<?=$n?>')"style="text-align:right;" ><img src="/img/error-icon.png" width="20" height="20"></a>
						   	</div>
						</div>
						<?php $n++;?>
					<?php endforeach ?>
				<?php endif ?>
			<div class="clear"></div>
			<div class="grid_16" style="background:#f7f7f7; border-bottom:1px solid #ddd; margin:0px 0px 5px 0px;">
			<div>
				<a href="#" id="add_acl" onclick="show_ad()" ><img src="/img/add.png" width="20" height="20"></a>
				<span style="font-size:24px;padding:15px;">New Acls</span>
				<?=$this->form->hidden('current', array('class' => 'inputbox', 'id' => 'current', 'value' => $group["name"])); ?>
				<?=$this->form->hidden('type', array('class' => 'inputbox', 'id' => 'type', 'value' => "")); ?>
			</div>
			<div id="new_acl" style="display:none;">
				<div class="clear"></div>
				<?php for ($i=($n+1) ; $i < ($n+5) ; $i++): ?>
						<span style="margin:0px 20px 0px 20px;">Route <?=$this->form->text('acl_route_'.$i, array('class' => 'inputbox', "id" => "acl_route_".$i)); ?> </span>
						<span style="margin:0px 20px 0px 20px;">Connection <?=$this->form->text('acl_connection_'.$i, array('class' => 'inputbox', "id" => "acl_connection_".$i)); ?></span>
					<div class="clear"></div>
				<?php endfor ?>
			</div>
			</div>
			<div class="clear"></div>
			<div class="grid_16" style="background:#f7f7f7; border-bottom:1px solid #ddd; margin:0px 0px 5px 0px;">
			<div>
				<a href="#" id="button_view_users" onclick="show_users()" ><img src="/img/add.png" width="20" height="20"></a>
				<span style="font-size:24px;padding:15px;">View Users</span>
			</div>
			<div id="view_users" style="display:none;">
				<div class="clear"></div>
				<?php if(!empty($users)): ?>
					<table id="orderTable" class="datatable" border="1">
						<thead>
							<tr>
								<td>Email</td>
								<td>First Name</td>
								<td>Last Name</td>
								<td><div style="text-align:center;"><a id="remove_button"><img src="/img/error-icon.png" width="20" height="20"></a></div></td>
							</tr>
						</thead>
						<tbody>
							<?php $t = 0; ?>
							<?php foreach($users as $user): ?>
							<tr>
								<td><?=$user["email"]?></td>
								<td><?=$user["firstname"]?></td>
								<td><?=$user["lastname"]?></td>
								<td><div style="text-align:center;"><?=$this->form->checkbox("user_".$t, array( 'value' => $user["_id"]))?></div></td>
							</tr>
							<?php $t++; ?>
							<?php endforeach ?>
						</tbody>
					</table>
				<?php endif ?>
					<div class="clear"></div>
			</div>
			</div>
		<br />
		<div style="text-align:center">
			<?=$this->form->submit('Update', array("style" => "width:100px;font-weight:bold;font-size:14px;"))?>
		</div>
		</div>
	<?php endif ?>
	<br />
	<div class="clear"></div>
	<?php if(empty($group)): ?>
		<div style="background:#f7f7f7; border-bottom:1px solid #ddd; margin:0px 0px 5px 0px; padding:5px;">
			<span style="font-size:22px;">Create a Group</span>
			<?=$this->form->create(null ,array('id'=>'addGroupForm','enctype' => "multipart/form-data")); ?>
			<span style="margin:0px 20px 0px 20px;">Type the name : <?=$this->form->text("add_group", array('class' => 'inputbox', "id" => "add_group")); ?></span>
			<?=$this->form->submit('Add', array("style" => "width:100px;font-weight:bold;font-size:12px;"))?>
		</div>
		<div style="background:#f7f7f7; border-bottom:1px solid #ddd; margin:0px 0px 5px 0px; padding:5px;">
			<span style="font-size:22px;">Remove a Group </span>
			<?=$this->form->create(null ,array('id'=>'removeGroupForm','enctype' => "multipart/form-data")); ?>
			<span style="margin:0px 20px 0px 20px;"><?=$this->form->select("remove_group", $select_groups, array("style" => "width:150px;font-weight:bold;font-size:13px;")); ?></span>
			<?=$this->form->submit('Remove', array("style" => "width:100px;font-weight:bold;font-size:12px;"))?>
		</div>
	<?php endif ?>
</div>
<script type="text/javascript" >
	function show_ad() {
		if ($("#new_acl").is(":hidden")) {
			$("#new_acl").show("slow");
		} else {
			$("#new_acl").slideUp();
		}
	};
	function show_users() {
		if ($("#view_users").is(":hidden")) {
			$("#view_users").show("slow");
		} else {
			$("#view_users").slideUp();
		}
	};
	function cancel(val) {
		$("#acl_" + val).slideUp();
			document.getElementById('acl_route_' + val).value = "";
			document.getElementById('acl_connection_' + val).value = "";
			document.getElementById('type').value = "cancel";
	};
	function remove_user(val) {
		$("#acl_" + val).slideUp();
			document.getElementById('acl_route_' + val).value = "";
			document.getElementById('acl_connection_' + val).value = "";
			document.getElementById('type').value = "cancel";
	};
</script>