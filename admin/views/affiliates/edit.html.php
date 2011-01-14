
<div class="grid_16">
	<h2 id="page-heading">Affiliate Edit Panel</h2>
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
				<td><?php echo $this->html->link('View/Edit Affliliat', 'affiliates/index' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<br>
<br>
<div class="grid_16">
	<?=$this->form->create($affiliate); ?>
		<?php $active = (($affiliate->active))? 'checked':''; ?>
		Activate: <?=$this->form->checkbox('active', array('checked'=>$active)); ?>
		<?php

			if(!is_string($affiliate->invitation_codes)){
				$codes = implode(' ', $affiliate->invitation_codes->data());
			}else{
				$codes = $affiliate->invitation_codes;
			}
		?>
		Affiliate Code:
		<?=$this->form->text('invitation_codes', array('value'=>$codes )); ?>

		<?=$this->form->submit('Edit'); ?>
	<?=$this->form->end(); ?>
</div>