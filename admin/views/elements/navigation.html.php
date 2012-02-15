<ul class="nav main">
	<li>
		<?php echo $this->html->link('Orders', 'Orders::index'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Search Orders', 'Orders::index'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Update Order Status', 'Orders::update'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Order Payment Status', 'Orders::payments'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Digital Items to be Fulfilled', 'Orders::digitalItemsToFulfill'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Fulfilled Digital Items', 'Orders::digitalItemsFulfilled'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Credits', '#'); ?>
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
		<?php echo $this->html->link('Events/Items', array('Base::selectEvent')); ?>
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
			<li>
				<?php echo $this->html->link('Bulk Cancelation of Items', 'Items::bulkCancel'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Services', array('Services::index')); ?>
		<ul>
		    <li>
				<?php echo $this->html->link('View Services', 'Services::index'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Add New Service', 'Services::add'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Banners', array('Banners::view')); ?>
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
				<?php echo $this->html->link('Sales by Days', 'Reports::salesDays'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Event Sales', 'Reports::eventSales'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Registered Users', 'Reports::registeredUsers'); ?>
			</li>
			<li>
				<?php echo $this->html->link('Services', 'Reports::services'); ?>
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
		<?php echo $this->html->link('Users', '#'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Search Users', 'Users::index'); ?>
			</li>
		</ul>
	</li>
	<li>
		<?php echo $this->html->link('Email Management', '#'); ?>
		<ul>
			<li>
				<?php echo $this->html->link('Email by Event', array('Base::selectEvent', 'args'=>'email')); ?>
			</li>
			<li>
				<?php echo $this->html->link('Bounced Emails', array('Bouncedemails::index')); ?>
			</li>

		</ul>
		
	</li>
	<li>
		<?php echo $this->html->link('Tickets Management', array('Tickets::view')); ?>
	</li>
	<li class="secondary">
		<?php echo $this->html->link('Logout', 'Users::logout'); ?>
	</li>

</ul>