<?=$this->html->script(array('tiny_mce/tiny_mce.js', 'swfupload.js', 'swfupload.queue.js', 'fileprogress.js', 'handlers.js', 'newData_upload.js', 'jquery.dataTables.js', 'jquery-ui-timepicker.min.js'));?>
<?=$this->html->style(array('swfupload', 'jquery_ui_blitzer', 'table', 'timepicker'));?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>

<div class="grid_11">
    <h3>Edit Services/Offers</h3>
    <?=$this->form->create($newData);?>
        <label>Name</label>
        <?=$this->form->text('name'); ?>
        <?=$this->form->error('name');?>
        <br/>
        <br/>
        <label>Enable</label>
        <?php
            if($newData->enabled){
                $checked = "checked";
            }else{
                $checked = "";
            }
        ?>
        <?=$this->form->checkbox("enabled", array('value' => "1", 'checked' => $checked)); ?>
        <br/>
        <br/>
        <label>Trigger Type</label>
        <?=$this->form->select('trigger_type', $triggers); ?>
        <br/>
        <br/>
        <label>Trigger Amount</label>
        <?=$this->form->text('trigger_value'); ?>
        <?=$this->form->error('trigger_value');?>
        <br/>
        <br/>
         <label>Quantity</label>
        <?=$this->form->text('in_stock'); ?>
        <br/>
        <br/>
        <label>Pop Up Action</label>
        <?=$this->form->select("trigger_action", $trigger_actions); ?>
        <br/>
        <br/>
        <label>Start Date:</label>
        <?=$this->form->text('start_date'); ?>
        <?=$this->form->error('start_date');?>
        <label>End Date:</label>
        <?=$this->form->text('end_date'); ?>
        <?=$this->form->error('end_date');?>
        <br/>
        <br/>
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
                        <?=$this->html->image("/image/$newData->logo_image.jpg", array('alt' => 'altText', 'width' => 100)); ?>
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
        <?=$this->form->submit('Edit'); ?>
    <?=$this->form->end();?>
</div>
<script type="text/javascript">
    jQuery(function($){
        $(".date").mask("99/99/9999");
    });
</script>