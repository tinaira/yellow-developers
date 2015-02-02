<?php list($name, $blog, $pagesMax) = $yellow->getSnippetArgs() ?>
<?php $pages = $blog->getChildren(!$blog->isVisible()) ?>
<?php $pages->filter("template", "blog")->sort("published", false)->limit($pagesMax) ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<div class="blogrecent">
<ul>
<?php foreach($pages as $page): ?>
<li><a href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("titleNavigation") ?></a></li>
<?php endforeach ?>
</ul>
</div>
