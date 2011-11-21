<h2 style="margin-bottom:20px;">Join with Facebook</h2>
<a href="javascript:;" onclick="fblogin();return false;"><img src="/img/sign_in_fb.png" class="fr"></a>
<br />
<h2 style="margin-top:30px;margin-bottom:20px;">Or join with email</h2>


<?php if (preg_match('/join/',$_SERVER['REQUEST_URI'])): ?>
	<form id="registerForm" method="post" onsubmit="_gaq.push(['_trackPageview', '/vpv/join']);">
<?php elseif (preg_match('/register/',$_SERVER['REQUEST_URI'])): ?>
	<form id="registerForm" method="post" onsubmit="_gaq.push(['_trackPageview', '/vpv/register']);">
<?php elseif (preg_match('/a/',$_SERVER['REQUEST_URI'])): ?>
	<form id="registerForm" method="post" onsubmit="_gaq.push(['_trackPageview', '/vpv/affiliate']);">
<?php else: ?>
	<form id="registerForm" method="post" onsubmit="_gaq.push(['_trackPageview', '/vpv/register']);">
<? endif; ?>

	<div style="width:70px; float:left">

	<?=$this->form->label('email', 'Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	</div>

	<div style="float:right">
	<?=$this->form->text('email', array('class' => 'inputbox', 'style' => 'width:190px')); ?>
	</div>
	<?=$this->form->error('email'); ?>
	
	<div class="clear"></div>
	<div style="width:70px; float:left">
		<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
			'escape' => false,
			'class' => 'required'
			));
		?>
	</div>

	<div style="float:right">
		<?=$this->form->text('confirmemail', array('class' => 'inputbox', 'style' => 'width:190px')); ?>
	</div>
	
	<?=$this->form->error('confirmemail'); ?>
	<?=$this->form->error('emailcheck'); ?>
	
	<div class="clear"></div>

	<div style="width:70px; float:left">
	<?=$this->form->label('password','Password <span>*</span>', array(
		'class'=>'required',
		'escape' => false
		));
	?>
	</div>

	<div style="float:right">


	<?=$this->form->password('password', array(
		'class'=>"inputbox",
		'name' => 'password',
		'id' => 'password', 'style' => 'width:190px'
		));
	?>
	</div>
	<?=$this->form->error('password'); ?>
	<?=$this->form->checkbox('terms', array(
		"checked" => "checked",
		'style'=>"float:left;margin-right:4px; display: none;"
		));
	?>

	<div class="clear"></div>
	<div style="font-size:11px;margin-top:10px; color:#999999;">
		By joining you accept our
		<?=$this->html->link('terms and conditions','pages/terms')?>.
	</div>

	<input class="button fr" type="button" value="Join Now" onclick="return setIframe();" style="width:100px; height 28px; font-weight:important;">

	<? //=$this->form->submit('Join Now', array('class' => 'button fr','style' => 'width:100px; height 28px; font-weight:important;'));?>

	<?=$this->form->error('terms'); ?>


<?=$this->form->end(); ?>

<div id="psm" style="display:none;"></div>
<div id="fbshoes" style="display:none;"></div>


<script type="text/javascript">

  function setIframe() {
	var tiframe = document.getElementById('psm').innerHTML = '<iframe src="/static/signup-tracking.html" style="border:none;width:1px;height:1px;" marginheight="0" marginwidth="0" frameborder="0"></iframe>';
<?php if (preg_match('/facebookshoes/',$_SERVER['REQUEST_URI'])): ?>
  	var fbshoesiframe = document.getElementById('fbshoes').innerHTML = '<iframe src="/static/facebookshoes-tracking.html" style="border:none;width:1px;height:1px;" marginheight="0" marginwidth="0" frameborder="0"></iframe>';
<?php endif; ?>
	setTimeout ( "pauseFunction()", 1500 );
  }

function pauseFunction ( )
{
  document.forms["registerForm"].submit();
}

</script>

<?php if (preg_match('/facebookshoes/',$_SERVER['REQUEST_URI'])): ?>
<!-- begin Marin Software Tracking Script -->
<script type='text/javascript'>
    var _mTrack = _mTrack || [];
    _mTrack.push(['trackPage']);

    (function() {
        var mClientId = '1146r8p12266';
        var mProto = ('https:' == document.location.protocol ? 'https://' : 'http://');
        var mHost = 'pro.marinsm.com';
        var mt = document.createElement('script'); mt.type = 'text/javascript'; mt.async = true;
        mt.src = mProto + mHost + '/tracker/async/' + mClientId + '.js';
        var fscr = document.getElementsByTagName('script')[0]; fscr.parentNode.insertBefore(mt, fscr);
    })();
</script>
<!-- end Copyright Marin Software -->
<?php endif; ?>
