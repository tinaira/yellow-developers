<?php list($name, $separator) = $yellow->getSnippetArgs() ?>
<?php if(!$separator) $separator = "&gt;" ?>
<div class="breadcrumbs">
<p>
<?php foreach($yellow->pages->path($yellow->page->getLocation(), true) as $page): ?>
<a href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("titleNavigation") ?></a> <?php if($page->getLocation() != $yellow->page->getLocation()) echo $separator ?> 
<?php endforeach ?>
</p>
</div>
