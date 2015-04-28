<?php list($name, $blog) = $yellow->getSnippetArgs() ?>
<?php $pages = $blog->getChildren(!$blog->isVisible()) ?>
<?php $pages->filter("template", "blog") ?>
<?php $tags = array(); ?>
<?php foreach($pages as $page) if($page->isExisting("tag")) foreach(preg_split("/,\s*/", $page->get("tag")) as $tag) ++$tags[$tag]; ?>
<?php if(!empty($tags)): ?>
<?php uksort($tags, strnatcasecmp); ?>
<?php $tagMinimum = min($tags); ?>
<?php $tagMaximum = max($tags); ?>
<?php $tagDivider = max(($tagMaximum - $tagMinimum)/4, 1); ?>
<?php foreach($tags as $key=>$value) $tags[$key] = "type".(1+intval(($value-$tagMinimum) / $tagDivider)); ?>
<?php endif ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<div class="blogtagcloud">
<ul>
<?php foreach($tags as $key=>$value): ?>
<li><a class="<?php echo $value?>" href="<?php echo $blog->getLocation().$yellow->toolbox->normaliseArgs("tag:$key") ?>"><?php echo htmlspecialchars($key) ?></a></li>
<?php endforeach ?>
</ul>
</div>
