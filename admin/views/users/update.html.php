<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->style(array('jquery_ui_blitzer.css', 'table'));?>
<?php
	$this->title(" - User Information");
?>
<?php if ($user): ?>
	<style type="text/css">
		td{font-family:Arial,sans-serif;font-size:14px;line-height:18px}
		img{border:none}
	</style>
	<table cellspacing="0" cellpadding="0" border="0" width="695">
		<tr>
			<td valign="top">
				<h2 class="gray mar-b">User Information</h2>
				<hr />
				<table border="0" cellspacing="5" cellpadding="5" width="100">
						<?php foreach ($info as $key => $value): ?>
							<?php if (in_array($key, array('lastlogin'))): ?>
								<tr><td><?=$key?></td><td><?=date('m-d-Y', $value['sec']);?></td></tr>
								<?php else: ?>
									<tr><td><?=ucfirst($key)?></td><td><?=$value?></td></tr>
								<?php endif ?>
						<?php endforeach ?>
				</table>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<h2 class="gray mar-b">Group Selection</h2>
				<hr />
				<div class="block" id="order-search">
					<fieldset>
						<?=$this->form->create(); ?>
						<table>
							<?php foreach ($groups as $group):
									//check groups of the user
									$checked = false;
									if(!empty($user["groups"])){
										foreach($user["groups"] as $u_group)
										{
											if($u_group["_id"] == $group["_id"]) $checked = true;
										}
									}?>
						<tr>
							<td><?=$group["name"]?></td>
							<td>
							<?php
							if($checked==true)
									echo $this->form->checkbox( $group["name"], array('value' => '1', 'checked' => 'checked'));
							else
									echo $this->form->checkbox( $group["name"], array('value' => '1'));
								?>
							</td>
						</tr>
						 	<?php endforeach ?>
						</table>
						<?=$this->form->submit('Update', array('style' => 'float:right; width:100px; margin: 0px 20px 0px 0px;'))?>
						<?=$this->form->end(); ?>
					</fieldset>
			</td>
		</tr>
	</table>
<?php else: ?>
	<strong>Sorry, we cannot locate the user that you are looking for.</strong>
<?php endif ?>