<table border="1" cellspacing="30" cellpadding="30">
    <tr>
        <th align="justify">
            Img Name
        </th>
        <th align="justify">
            Img
        </th>
        <th align="justify">
            Tag
        </th>
    </tr>
    <tr>
        <td align="center" width="100">
            <?php echo $fileName; ?>
        </td>
        <td align="center">
            <a href="/image/<?php echo $id; ?>.jpg" target="_blank" ><?=$this->html->image("/image/$id.jpg", array('alt' => 'altText', 'width' => 100)); ?></a>
        </td>
        <td align="center">
            <?php echo $tag; ?>
        </td>
    </tr>
</table>