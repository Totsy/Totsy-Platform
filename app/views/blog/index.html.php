<h1 class="p-header">Totsy Blog</h1>

	<?php foreach ($rss->item as $item): ?>
        <div style="margin:15px 35px;">
          <h3 style="font-size:16px;"><?php echo $item->title?>
              <span style="font-size:12px!important; color:#CCC; float:right; font-style:italic;">
                <?=$item->pubDate?>
              </span>
          </h3>
          <hr style="color:#CCC;"/>
          <p><?php echo $item->description?></p>
        </div>
    <?php endforeach ?>
