<?php /* Sitemapxml template 0.1.5 */ ?>
<?php if($yellow->getRequestHandler() == "webinterface") { require_once("default.php"); return; } ?>
<?php $pages = $yellow->pages->index(false, false, 3) ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<?php $yellow->page->header("Content-Type: text/xml; charset=\"utf-8\"") ?>
<?php echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($pages as $page): ?>
<url><loc><?php echo $page->getUrl() ?></loc></url>
<?php endforeach ?>
</urlset>
