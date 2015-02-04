<?php list($name, $blog) = $yellow->getSnippetArgs() ?>
<?php $pages = $blog->getChildren(!$blog->isVisible()) ?>
<?php $pages->filter("template", "blog") ?>
<?php $months = array(); ?>
<?php foreach($pages as $page) if(preg_match("/^(\d+\-\d+)\-/", $page->get("published"), $matches)) ++$months[$matches[1]]; ?>
<?php uksort($months, strnatcasecmp); ?>
<?php $months = array_reverse($months); ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<div class="blogarchive">
<ul>
<?php foreach($months as $key=>$value): ?>
<li><a href="<?php echo $blog->getLocation().$yellow->toolbox->normaliseArgs("published:$key") ?>"><?php echo htmlspecialchars($yellow->text->normaliseDate($key)) ?></a> </li>
<?php endforeach ?>
</ul>
</div>
