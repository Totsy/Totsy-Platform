<?php echo $this->html->script(array('tiny_mce/tiny_mce.js', 'swfupload.js', 'swfupload.queue.js', 'fileprogress.js', 'handlers.js', 'service_upload.js', 'jquery.dataTables.js', 'jquery-ui-timepicker.min.js'));?>
<?php echo $this->html->style(array('swfupload', 'jquery_ui_blitzer', 'table', 'timepicker'));?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>
<style type="text/css">
    label {font-size:15px;}
</style>
<?php
    if($newData->enabled){
        $checked = "checked";
    }else{
        $checked = "";
    }
?>
<div class="grid_13">
    <h3>Add Services/Offers</h3>
    <?php echo $this->form->create($newData);?>
        <div class="box block">
            <h2 class="box">General Info</h2>
            <br>
            <label>Name</label>
            <?php echo $this->form->text('name'); ?>
            <?php echo $this->form->error('name');?>
            <br/>
            <br/>
            <label>Enable</label>
            <?php echo $this->form->checkbox("enabled", array('value' => "1", 'checked' => 'checked')); ?>
            <br/>
            <br/>
            <label>Quantity - If Offer/service has a limited quantity</label>
            <?php echo $this->form->text('in_stock'); ?>
            <br/>
            <br/>
            <label>Start Date:</label>
            <?php echo $this->form->text('start_date', array('class' => 'date')); ?>
            <?php echo $this->form->error('start_date');?>
            <br/>
            <label>End Date:</label>
            <?php echo $this->form->text('end_date', array('class' => 'date')); ?>
            <?php echo $this->form->error('end_date');?>
            <br/>
            <br/>
        </div>

        <div class="box">
            <h2 class="box"> Eligible Triggers </h2>
            <br>
            <label>Trigger Type</label>
            <?php echo $this->form->select('trigger_type', $triggers, array("id" => "trigger_type", "value"=>$newData->eligible_trigger->trigger_type)); ?>
            <br/>
            <br/>
            <label>Trigger Amount</label>
            <?php echo $this->form->text('trigger_value', array('value' => $newData->eligible_trigger->trigger_value)); ?>
            <?php echo $this->form->error('trigger_value'); ?>
            <br/>
            <br/>
            <label>Pop Up Action</label>
            <?php echo $this->form->select("trigger_action", $trigger_actions, array("id" => "trigger_action")); ?>
            <br/>
            <br/>
            <label>Pop Up Wording</label> <br/>
            <?php echo $this->form->textarea("popup_text", array("cols" => 75, "rows" => 15, "id" => "popup_text", "value" => $newData->eligible_trigger->popup_text )); ?>
            <br/>
            <br/>
        </div>
        <br/>
        <?php
            if($newData->upsell_trigger){
                $checked = "checked";
                $type = $newData->upsell_trigger->trigger_type;
                $min_value = $newData->upsell_trigger->min_value;
                $max_value = $newData->upsell_trigger->max_value;
                $action = $newData->upsell_trigger->trigger_action;
                $text = $newData->upsell_trigger->upsell_popup_text;
            }else{
                $checked = "";
                $type = "";
                $min_value = "";
                $max_value = "";
                $action = "";
                $text = "";
            }
        ?>
         <div class="box">
            <h2 class="box"> <?php echo $this->form->checkbox('upsell_active', array("value" => "1", "checked" => $checked, "id" => "upsell_active")); ?> &nbsp; Upsell Triggers </h2>
            <br>
            <label>Trigger Type</label>
            <?php echo $this->form->select('upsell_trigger_type', $triggers, array('id' => 'upsell_trigger_type', 'value' => $type)); ?>
            <br/>
            <br/>
            <label>Trigger Range</label><br/>
            Min:
            <?php echo $this->form->text('upsell_trigger_min', array("class" => "range","style" => "width:20%", "value" => $min_value)); ?>
            <?php echo $this->form->error('upsell_trigger_min');?>
            &nbsp;
            Max:
             <?php echo $this->form->text('upsell_trigger_max', array("class" => "range", "style" => "width:20%", "value" => $max_value)); ?>
            <?php echo $this->form->error('upsell_trigger_max');?>
            <br/>
            <br/>
            <label>Pop Up Action</label>
            <?php echo $this->form->select("upsell_trigger_action", $trigger_actions, array('id' => "upsell_trigger_action",'value' => $action)); ?>
            <br/>
            <br/>
            <label>Pop Up Wording</label> <br/>
            <?php echo $this->form->textarea("upsell_popup_text", array("cols" => 75, "rows" => 15, 'id' => "upsell_popup_text",'value' => $text)); ?>
            <br/>
            <br/>
        </div>
        <div id="newData_images">
            <table border="1" cellspacing="30" cellpadding="30">
                <tr>
                    <th align="justify">
                        Img Name
                    </th>
                    <th align="justify">
                        Img
                    </th>
                </tr>
                <tr>
                    <td align="center" width="100">
                        <?php echo $newData->name; ?>
                    </td>
                    <td align="center">
                        <a href="/image/<?php echo $newData->logo_image; ?>.jpg" target="_blank">
                        <?php echo $this->html->image("/image/$newData->logo_image.jpg", array('alt' => 'altText', 'width' => 100)); ?>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <h5 id="uploaded_media">Uploaded Media</h5>
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
                            <input id="islogo" value='1' name="islogo" type="checkbox" onclick="isLogo(upload1);" checked/> Is Logo
                            <br />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <?php echo $this->form->submit('Edit'); ?>
    <?php echo $this->form->end();?>
</div>
<script type="text/javascript">
    jQuery(function($){
        $(".date").mask("99/99/9999");
    });
    $(document).ready(function(){
        $("#upsell_popup_text").attr('disabled', 'disabled');
        EnableField();
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