
<div class="grid_16">
	<h2 id="page-heading">Affiliate Add Panel</h2>
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

<div class="grid_16">
	<?=$this->form->create(); ?>

	Activate: <?=$this->form->checkbox('active', array('checked'=>'checked')); ?>

	Affiliate Code:
	<?=$this->form->text('invitation_codes'); ?>

	<?=$this->form->submit('Create'); ?>
	<?=$this->form->end(); ?>
</div>