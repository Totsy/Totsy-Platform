<?xml version="1.0" encoding="utf-8"?>
<deleteCustomerProfileRequest xmlns="<?=$gateways['schema']['profile']; ?>">

<?=$this->view()->render('template', compact('config'), array('template' => 'authentication')); ?>
<customerProfileId><?=$id; ?></customerProfileId>
</deleteCustomerProfileRequest>
