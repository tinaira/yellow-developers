<div class="navigationside">
<h1><a href="<?php echo $yellow->page->base."/" ?>"><?php echo $yellow->page->getHtml("sitename") ?></a></h1>
<ul>
<?php foreach($yellow->pages->top() as $page): ?>
<li><a<?php echo $page->isActive() ? " class=\"active\"" : "" ?> href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("titleNavigation") ?></a></li>
<?php endforeach ?>
</ul>
</div>
