<?php if($code) :?>

        <table>
            <thead>
                <tr>
                    <th> Code </th>
                    <th> Enabled </th>
                    <th> Change Status </th>
                </tr>
            </thead>
            <tbody>
                <tr id="<?=$code->_id?>">
                    <td><?=$code->code;?> </td>
                    <td><?=$code->enabled?></td>
                    <?php if ($code->enabled): ?>
                        <td><a target="#codes" class="change_status" id="disable" href="#">Disable </a></td>
                    <?php else: ?>
                        <td><a target="#codes" class="change_status" id="enable" href="#" >Enable </a></td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>

<?php endif; ?>

<script type="text/javascript">
$('.change_status').click(function(){
    var item = $(this);
    var code_search = $("input[name=code_search]").val();
    var parent_id = "<?=$code->parent_id;?>";
    var code_id = "<?=$code->_id;?>";
    var status = item.attr('id');
    var dataString = 'code_search='+ code_search + '&parent_id=' + parent_id;
    dataString = dataString + '&change_status='+ status + '&code_id=' + code_id;
        var target = $(item.attr('target'));

    $.ajax({
      type: "POST",
      url: "/promocodes/findPromo",
      data: dataString,
      success: function(data) {
        target.html(data);
      }
    });
    return false;
});
</script>
