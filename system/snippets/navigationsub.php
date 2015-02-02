<?php $pages = $yellow->pages->create() ?>
<?php $page = $yellow->page->getParentTop(false) ?>
<?php if($page) $pages = $page->getChildren() ?>
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
