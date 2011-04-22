<?=$this->html->script(array('tiny_mce/tiny_mce.js', 'swfupload.js', 'swfupload.queue.js', 'fileprogress.js', 'handlers.js', 'service_upload.js'));?>
<?=$this->html->style(array('swfupload', 'jquery_ui_blitzer'));?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<style type="text/css">
    label {font-size:15px;}
</style>
<div class="grid_13">
    <h3>Add Services/Offers</h3>
    <?=$this->form->create($service);?>
        <div class="box block">
            <h2 class="box">General Info</h2>
            <br>
            <label>Name</label>
            <?=$this->form->text('name'); ?>
            <?=$this->form->error('name');?>
            <br/>
            <br/>
            <label>Enable</label>
            <?=$this->form->checkbox("enabled", array('value' => "1", 'checked' => 'checked')); ?>
            <br/>
            <br/>
            <label>Quantity - If Offer/service has a limited quantity</label>
            <?=$this->form->text('in_stock'); ?>
            <br/>
            <br/>
            <label>Start Date:</label>
            <?=$this->form->text('start_date', array('class' => 'date')); ?>
            <?=$this->form->error('start_date');?>
            <br/>
            <label>End Date:</label>
            <?=$this->form->text('end_date', array('class' => 'date')); ?>
            <?=$this->form->error('end_date');?>
            <br/>
            <br/>
        </div>

        <div class="box">
            <h2 class="box"> Eligible Triggers </h2>
            <br>
            <label>Trigger Type</label>
            <?=$this->form->select('trigger_type', $triggers, array("id" => "trigger_type")); ?>
            <br/>
            <br/>
            <label>Trigger Amount</label>
            <?=$this->form->text('trigger_value'); ?>
            <?=$this->form->error('trigger_value'); ?>
            <br/>
            <br/>
            <label>Pop Up Action</label>
            <?=$this->form->select("trigger_action", $trigger_actions, array("id" => "trigger_action")); ?>
            <br/>
            <br/>
            <label>Pop Up Wording</label> <br/>
            <?=$this->form->textarea("popup_text", array("cols" => 75, "rows" => 15, "id" => "popup_text")); ?>
            <br/>
            <br/>
        </div>
        <br/>
         <div class="box">
            <h2 class="box"> <?=$this->form->checkbox('upsell_active', array("value" => "1", "id" => "upsell_active")); ?> &nbsp; Upsell Triggers </h2>
            <br>
            <label>Trigger Type</label>
            <?=$this->form->select('upsell_trigger_type', $triggers, array('id' => 'upsell_trigger_type')); ?>
            <br/>
            <br/>
            <label>Trigger Range</label><br/>
            Min:
            <?=$this->form->text('upsell_trigger_min', array("class" => "range","style" => "width:20%")); ?>
            <?=$this->form->error('upsell_trigger_min');?>
            &nbsp;
            Max:
             <?=$this->form->text('upsell_trigger_max', array("class" => "range", "style" => "width:20%")); ?>
            <?=$this->form->error('upsell_trigger_max');?>
            <br/>
            <br/>
            <label>Pop Up Action</label>
            <?=$this->form->select("upsell_trigger_action", $trigger_actions, array('id' => "upsell_trigger_action")); ?>
            <br/>
            <br/>
            <label>Pop Up Wording</label> <br/>
            <?=$this->form->textarea("upsell_popup_text", array("cols" => 75, "rows" => 15, 'id' => "upsell_popup_text")); ?>
            <br/>
            <br/>
        </div>

        <h3 id="uploaded_media">Uploaded Media</h3>
        <div id="fileInfo"></div>
        <br>
        <br>
        <table>
            <tr valign="top">
                <td>
                    <div>
                        <div class="fieldset flash" id="fsUploadProgress1">
                            <span class="legend">Upload Status</span>
                        </div>
                        <div style="padding-left: 5px;">
                            <span id="spanButtonPlaceholder1"></span>
                            <input id="btnCancel1" type="button" value="Cancel Uploads" onclick="cancelQueue(upload1);" disabled="disabled" style="margin-left: 2px; height: 22px; font-size: 8pt;" />
                            <input id="islogo" value='1' name="islogo" type="checkbox" checked"/> Is Logo
                            <br />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <?=$this->form->submit('Create'); ?>
    <?=$this->form->end();?>
</div>
<script type="text/javascript">
jQuery(function($){
 	$(".date").mask("99/99/9999");
});
$(document).ready(function(){
    $("#upsell_popup_text").attr('disabled', 'disabled');
    EnableField();
    isLogo(upload1);
    $('#upsell_active').change(function(){
        EnableField();
    });

    $("#upsell_trigger_action").change(function(){
        if($("#upsell_trigger_action").val() == "pop_up"){
            $("#upsell_popup_text").removeAttr('disabled');
        }else{
             $("#upsell_popup_text").attr('disabled', 'disabled');
        }
    });
     $("#trigger_action").change(function(){
        if($("#trigger_action").val() == "pop_up"){
            $("#popup_text").removeAttr('disabled');
        }else{
             $("#popup_text").attr('disabled', 'disabled');
        }
    });
});
function EnableField(){
    if($('#upsell_active:checked').val() == '1'){
            $('#upsell_trigger_type').removeAttr('disabled');
            $('.range').removeAttr('disabled');
            $("#upsell_trigger_action").removeAttr('disabled');
        }else{
            $('#upsell_trigger_type').attr('disabled', 'disabled');
            $('.range').attr('disabled', 'disabled');
            $("#upsell_popup_text").attr('disabled', 'disabled');
            $("#upsell_trigger_action").attr('disabled', 'disabled');
        }
}
</script>