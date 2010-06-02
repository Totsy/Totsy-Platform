<?php foreach($pages as $page): ?>
<article>
    <h1><?=$page->title ?></h1>
    <p><?=$page->body ?></p>
</article>
<?php endforeach; ?>