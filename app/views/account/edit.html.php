<?=$this->html->script('mootools-1.2.4-core-nc.js');?>
<?=$this->html->script('mootools-1.2.4.4-more.js');?>
<?=$this->html->script('formcheck.js');?>
<?=$this->html->script('en.js');?>
<?=$this->html->style('style');?>

<script type="text/javascript">
    window.addEvent('domready', function(){
        new FormCheck('addressForm');
    });
</script>

<div class="tl"></div>
<div class="tr"></div>
<div id="page">

	<h2 class="gray mar-b">Edit Account Information</h2>
	<fieldset id="" class="">
		<legend>Address Information</legend>
		<div>
		</div>
		<?=$this->form->create('',array('id'=>'addressForm'));?>
		<?=$this->form->field('address1', array(
				'label' => 'Address 1' ,
				'class'=>"validate['required']", 
				'type' => 'text', 
				'name' => 'test'
			))?>
		<?=$this->form->field('address2', array(
				'label' => 'Address 2', 
				'type'=> 'text'
			));?>
		<?=$this->form->field('City', array(
				'label' => 'City'
			));?>
		<?=$this->form->field('State', array(
				'label' => 'State', 
				'class'=>'required'
			));?>
		<?=$this->form->field('postalcode', array(
				'label' => 'Postal Code', 
				'class'=>'required'
			));?>
		<?=$this->form->field('country', array(
				'label' => 'Country', 
				'class'=>'required'
			));?>
		<button type="submit" name="submit" class="flex-btn fr"><span>Submit</span></button>
		<?=$this->form->end();?>	
	</fieldset>
	
</div>
<div class="bl"></div>
<div class="br"></div>