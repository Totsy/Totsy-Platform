<h3 id="current_images">Current Categories</h3>
<hr />
    <table border="1" cellspacing="30" cellpadding="30" id="current_categories">
    <tr>
        <th align="justify">
            Image
        </th>
        <th align="justify">
            Category
        </th>
        <th align="justify">
            Code
        </th>
        <th align="justify">
            Remove
        </th>
    </tr>
        <?php foreach($categories['category'] as $image):?>
            <tr id="<?=$image['background_image'];?>">
                <td align="center">
                    <?php
                            $catImage = "/image/{$image['background_image']}.png";
                    ?>
                    <?=$this->html->image("$catImage", array('width' => 100, 'alt' => 'altText')); ?>
                </td>

                <td align="center">
                    <?php
                            $category = $image['name'];
                            $id = $image['background_image'];
                    ?>
                    <?=$this->form->text("affiliate_category[$id]", array('value' => $category, 'autocomplete'=>'on', 'class' => 'affiliate_category', 'id'=>"category_" . $id)); ?>
                </td>
                <td align="center">
                    <?php
                        $code = $image['code'];
                        $options = array_combine($categories->invitation_codes->data(),$categories->invitation_codes->data());
                        $selection = array_merge($options,array('all' => 'all'));
                    ?>
                    <?=$this->form->select("apply_code[$id]", $selection,array('class' => "relevantCodes", "value" => $code));?>
                </td>
                 <td align="center">
                     <?=$this->html->image('/img/agile_uploader/trash-icon.png', array(
                        "class" => "dynamic_page",
                        'width' => 'auto',
                        'height' => 'auto',
                        'alt' => 'altText',
                        'style' => 'cursor:pointer')); ?>
                    <input type="hidden" name="img[]" value="<?php echo $id; ?>"/>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
<script>
//remove dynamic page
$('.dynamic_page').click(function(){
    var id = $(this).parents('tr:first').attr('id');
    var page = $('#' + id);

    $.ajax({
        async: false,
        type: "DELETE",
        url: "/files/delete/"+id,
        success: function() {
            page.remove();
        }
    });
});
</script>