<?php $pages = $yellow->pages->top() ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<div class="navigation">
<ul>
<?php foreach($pages as $page): ?>
<li><a<?php echo $page->isActive() ? " class=\"active\"" : "" ?> href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("titleNavigation") ?></a></li>
<?php endforeach ?>
</ul>
</div>
<?php $page = $yellow->page->getParentTop(false) ?>
<?php $pages = $page ? $page->getChildren(): $yellow->pages->clean() ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<?php if(count($pages)): ?>
<div class="navigationsub">
<ul>
<?php foreach($pages as $page): ?>
<li><a<?php echo $page->isActive() ? " class=\"active\"" : "" ?> href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("titleNavigation") ?></a></li>
<?php endforeach ?>
</ul>
</div>
<?php endif ?>
<div class="navigation-banner"></div>
