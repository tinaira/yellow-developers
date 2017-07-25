<?php $pages = $yellow->pages->index()->filter("template", "blog")->sort("published", false)->limit(5) ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<?php $yellow->page->setHeader("Cache-Control", "max-age=60") ?>
<div class="content">
<div class="main">
<h1><?php echo $yellow->page->getHtml("titleContent") ?></h1>
<?php echo $yellow->page->getContent() ?>
<?php foreach($pages as $page): ?>
<h3><a href="<?php echo $page->getLocation(true) ?>"><?php echo $page->getDateHtml("published") ?> &nbsp; <?php echo $page->getHtml("title") ?></a></h3>
<?php endforeach ?>
</div>
</div>
