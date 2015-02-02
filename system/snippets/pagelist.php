<?php list($name, $pages, $style, $size) = $yellow->getSnippetArgs() ?>
<?php if(!is_object($pages)) $pages = $yellow->page->getChildren() ?>
<?php if(!$style) $style = "pagelist" ?>
<?php if(!$size) $size = "100%" ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<ul class="<?php echo htmlspecialchars($style) ?>">
<?php foreach($pages as $page): ?>
<?php $fileName = $src = basename($page->location).".jpg" ?>
<?php if($yellow->plugins->isExisting("image")): ?>
<?php list($src, $width, $height) = $yellow->plugins->get("image")->getImageInfo($fileName, $size, $size); ?>
<?php endif ?>
<li><a href="<?php echo $page->getLocation() ?>"><img src="<?php echo htmlspecialchars($src) ?>" width="<?php echo htmlspecialchars($width) ?>" height="<?php echo htmlspecialchars($height) ?>" alt="<?php echo $page->getHtml("title") ?>" title="<?php echo $page->getHtml("title") ?>" /></a><br /><a href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("title") ?></a></li>
<?php endforeach ?>
</ul>
