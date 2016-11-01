<?php $pages = $yellow->pages->top() ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<div class="navigation">
<ul>
<?php foreach($pages as $page): ?>
<li><a<?php echo $page->isActive() ? " class=\"active\"" : "" ?> href="<?php echo $page->getLocation(true) ?>"><?php echo $page->getHtml("titleNavigation") ?></a></li>
<?php endforeach ?>
<li><a href="https://twitter.com/datenstromse"><i class="fa fa-twitter fa-lg"></i></a></li>
<li><a href="https://github.com/datenstrom"><i class="fa fa-github fa-lg"></i></a></li>
<li><a href="https://instagram.com/datenstromse"><i class="fa fa-instagram fa-lg"></i></a></li>
</ul>
</div>
<div class="navigation-banner"></div>
