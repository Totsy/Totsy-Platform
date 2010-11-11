<ul class="nav main">
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
		<?php echo $this->html->link('Event/Item Management', 'Events::index'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Add New Event', 'Events::add'); ?>
			</li>
			<li>
				<?php echo $this->html->link('View Events', 'Events::index'); ?>
			</li>
			<li>
				<?php echo $this->html->link('View Items', 'Items::index'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Reports', '#'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Affiliate Report', 'Reports::affiliate'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Logistics', 'Reports::logistics'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Sales', 'Reports::sales'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Event Sales', 'Reports::eventSales'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Promotions', '#'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Create Promocode', '#'); ?>
			</li>
			<li>
				<?php echo $this->html->link('View/Edit Promocodes', '#'); ?>
			</li>
			<li>
				<?php echo $this->html->link('View Promotions', '#'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('User Management', '#'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Search Users', 'Users::index'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Email Management', 'Emails::index'); ?>
	</li>
	<li class="secondary">
		<?php echo $this->html->link('Logout', 'Users::logout'); ?>
	</li>
	
</ul>