<ul class="nav main">
	<li>
		<?php echo $this->html->link('Dashboard', '/'); ?>
		<ul>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Order Management', 'Orders::index'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Search Orders', 'Orders::index'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Update Order Status', 'Orders::update'); ?>
			</li>
		</ul>
	</li>
	
	<li>
		<?php echo $this->html->link('Event Management', 'Events::index'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Add New Event', 'Events::add'); ?>
			</li>
		</ul>
	</li>
	
	<li>
		<?php echo $this->html->link('Item Management', 'Items::index'); ?>
	</li>
	<li>
		<?php echo $this->html->link('Reports', '#'); ?>
	</li>
	<li>
		<?php echo $this->html->link('User Management', '#'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('User Credits', '#'); ?>
			</li>
			<li>
				<?php echo $this->html->link('User Info', '#'); ?>
			</li>
		</ul>
	</li>
	
	<li class="secondary">
		<?php echo $this->html->link('Logout', 'Users::logout'); ?>
	</li>
	
</ul>