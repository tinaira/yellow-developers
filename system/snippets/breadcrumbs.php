<?php list($name, $separator) = $yellow->getSnippetArgs() ?>
<?php if(!$separator) $separator = ">" ?>
<?php $pages = $yellow->pages->path($yellow->page->getLocation(), true) ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<div class="breadcrumbs">
<p>
<?php foreach($pages as $page): ?>
<a href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("titleNavigation") ?></a> <?php if($page->getLocation() != $yellow->page->getLocation()) echo htmlspecialchars($separator) ?> 
<?php endforeach ?>
</p>
</div>
