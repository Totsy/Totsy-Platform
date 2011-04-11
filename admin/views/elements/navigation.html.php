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
		<?php echo $this->html->link('Credit Management', '#'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Report on Credits', '#'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Apply Credit by Event', array('Base::selectEvent', 'args'=>'credits')); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Event/Item Management', array('Base::selectEvent')); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Add New Event', 'Events::add'); ?>
			</li>
			<li>
				<?php echo $this->html->link('View Events', array('Base::selectEvent')); ?>
			</li>
			<li>
				<?php echo $this->html->link('Search for Items', 'Items::search'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Banner Management', array('Banners::view')); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Add New Banner', 'Banners::add'); ?>
			</li>
			<li>
				<?php echo $this->html->link('View Banners', array('Banners::view')); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Reports', '#'); ?>
		<ul>
            <li>
				<?php echo $this->html->link('Google Analytics', 'Reports::googleAnalytics'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Affiliate Report', 'Reports::affiliate'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Logistics', array('Base::selectEvent', 'args'=>'logistics')); ?>
			</li>
			<li>
				<?php echo $this->html->link('Sales', 'Reports::sales'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Sale Details', 'Reports::saledetail'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Event Sales', 'Reports::eventSales'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Registered Users', 'Reports::registeredUsers'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Marketing', '#'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Promocode', 'promocodes/index'); ?>
				<?php echo $this->html->link('Affiliate', 'Affiliates::index'); ?>
				
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
		<?php echo $this->html->link('Email Management', array('Base::selectEvent', 'args'=>'email')); ?>
	</li>
	<li>
		<?php echo $this->html->link('Group Management', array('Groups::index')); ?>
	</li>
	<li class="secondary">
		<?php echo $this->html->link('Logout', 'Users::logout'); ?>
	</li>
	
</ul>