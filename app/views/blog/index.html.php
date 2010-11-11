<h1 class="p-header">Totsy Blog</h1>

	<?php foreach ($rss->item as $item): ?>
        <div style="margin:15px 35px;">
          <h3 style="font-size:16px;"><?php echo $item->title?></h3>
          <span style="font-size:12px!important; color:#CCC; font-style:italic;">
                <?=$item->pubDate?>
              </span>
          <hr style="color:#CCC;"/>
          <p><?php echo $item->description?></p>
        </div>
    <?php endforeach ?>
