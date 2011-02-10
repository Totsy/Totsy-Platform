<?=$this->html->script('jquery-1.4.2');?>
<div>
	<h2>Groups</h2><br />
	<h4> - Update ACLs</h4>
	<?=$this->form->create(null ,array('id'=>'groupsFrom')); ?>
	<?=$this->form->select("select_group", $select_groups, array("style" => "width:150px;font-weight:bold;font-size:13px;", "onChange" => 'this.form.submit();', 'value' => $group["name"])); ?><br />
	<?php if(!empty($group)): ?>
		<br />
			<div id="table_wrap" class="grid_16">
				<div id="title_row" class="grid_16 alpha">
					<h4><?=$group["name"]?></h4>
					<p>Total Users : <?=$group["total_users"]?></p>
				</div>
				<?php $n=0;?>
				<?php if (!empty($group["acls"])): ?>
					<?php foreach($group["acls"] as $acl):?>
						<div id="acl_<?=$n?>" class="grid_15">
							<div id="info_row" class="grid_13 alpha">
								<h5>Acl <?=$n?></h5>
								Route <?=$this->form->text("acl_route_".$n, array('class' => 'inputbox', 'value' => $acl["route"], "id" => "acl_route_".$n )); ?>
								Connection <?=$this->form->text("acl_connection_".$n, array('class' => 'inputbox', 'value' => $acl["connection"], "id" => "acl_connection_".$n )); ?>
							</div>	
						 	<div id="cross_row" class="grid_1 omega"> 
						   		<a href="#" id="cancel_button" onclick="cancel('<?=$n?>')"style="text-align:right;" ><img src="/img/error-icon.png" width=20 height=20></a>
						   	</div>
						</div>
						<?php $n++;?>
					<?php endforeach ?>
				<?php endif ?>
			<div class="grid_15">
				<a href="#" id="add_acl" onclick="show_ad()" ><img src="/img/add.png" width=20 height=20></a>
				<?=$this->form->hidden('current', array('class' => 'inputbox', 'id' => 'current', 'value' => $group["name"])); ?>
				<?=$this->form->hidden('type', array('class' => 'inputbox', 'id' => 'type', 'value' => "")); ?>
			</div>
			<div id="new_acl" class="grid_15" style="display:none">
				<h5>New Acl</h5>
				<?php for ($i=($n+1) ; $i < ($n+5) ; $i++): ?>
					<div>
						Route <?=$this->form->text('acl_route_'.$i, array('class' => 'inputbox', "id" => "acl_route_".$i)); ?> 
						Connection <?=$this->form->text('acl_connection_'.$i, array('class' => 'inputbox', "id" => "acl_connection_".$i)); ?>
					</div>
				<?php endfor ?>
			</div>
		</div>
		<br />
	<?php $j++; ?>
		<div style="text-align:center">
			<?=$this->form->submit('Update', array("style" => "width:100px;font-weight:bold;font-size:14px;"))?>
		</div>
	<?php endif ?>
	<br />
	<?php if(empty($group)): ?>
		<div>
			<h4> - Create a Group </h4>
			<?=$this->form->create(null ,array('id'=>'addGroupForm','enctype' => "multipart/form-data")); ?>
			Type the name : <?=$this->form->text("add_group", array('class' => 'inputbox', "id" => "add_group")); ?>
			<?=$this->form->submit('Add', array("style" => "width:100px;font-weight:bold;font-size:12px;"))?>
		</div>
		<br />
		<div>
			<h4> - Remove a Group </h4>
			<?=$this->form->create(null ,array('id'=>'removeGroupForm','enctype' => "multipart/form-data")); ?>
			Type the name : <?=$this->form->text("remove_group", array('class' => 'inputbox', "id" => "remove_group")); ?>
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
	function cancel(val) {
		$("#acl_" + val).slideUp();
			document.getElementById('acl_route_' + val).value = "";
			document.getElementById('acl_connection_' + val).value = "";
			document.getElementById('type').value = "cancel";
	};
</script>