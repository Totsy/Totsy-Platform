<script>
function temp(){
	<?php if($output){ ?>
		<?php if($output=="success"){ ?>
		parent.window.location = "/events/edit/<?=$_id?>/#event_items";
		parent.location.reload(true);
		<?php }else{ ?>
		parent.document.getElementById("upload_error").innerHTML = "<?=$output?>";
		<?php } ?>
	<?php } ?>
}

</script>
	<link rel="stylesheet" type="text/css" href="/css/reset.css" /><link rel="stylesheet" type="text/css" href="/css/debug.css" /><link rel="stylesheet" type="text/css" href="/css/text.css" /><link rel="stylesheet" type="text/css" href="/css/960.css" /><!--[if IE 6]><link rel="stylesheet" type="text/css" href="/css/ie6.css" /><![endif]--><!--[if IE 7]><link rel="stylesheet" type="text/css" href="/css/ie6.css" /><![endif]--><link rel="stylesheet" type="text/css" href="/css/ie6.css" /><!--[if IE 7]><link rel="stylesheet" type="text/css" href="/css/ie.css" /><![endif]--><link rel="stylesheet" type="text/css" href="/css/ie.css" /><link rel="stylesheet" type="text/css" href="/css/layout.css" /><link rel="stylesheet" type="text/css" href="/css/nav.css" /><link rel="stylesheet" type="text/css" href="/css/flash.css" /><link rel="stylesheet" type="text/css" href="/css/custom.css" /><script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script><script type="text/javascript" src="/js/jquery-fluid16.js"></script><script type="text/javascript" src="/js/jquery-ui-1.8.2.custom.min.js"></script>
	

<body onload="temp()">
<?=$this->form->create(null, array('enctype' => "multipart/form-data")); ?>
	<h3 id="">Upload Items</h3>
    <hr />
	<p>Please select the default option for all items being uploaded:</p>
		<input type="radio" name="enable_items" value="1" id="enable_items"> Enable All <br>
		<input type="radio" name="enable_items" value="0" id="enable_items" checked> Disable All <br><br>
	<p>Add "Final Sale" to the item description?:</p>
		<input type="radio" name="enable_finalsale" value="1" id="enable_finalsale" checked>Yes <br>
		<input type="radio" name="enable_finalsale" value="0" id="enable_finalsale">No<br><br>

	<?=$this->form->file('upload_file'); ?>
	<?=$this->form->submit('Update Event')?>

<br><br>
<?=$this->form->end(); ?>

</body>