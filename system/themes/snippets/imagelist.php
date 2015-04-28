<?php list($name, $pattern, $style, $size) = $yellow->getSnippetArgs() ?>
<?php $files = !$pattern ? $yellow->page->getFiles(true) : $yellow->files->index(true, true)->match("/$pattern/") ?>
<?php if(!$style) $style = "imagelist" ?>
<?php if(!$size) $size = "100%" ?>
<ul class="<?php echo htmlspecialchars($style) ?>">
<?php foreach($files as $file): ?>
<?php if($yellow->plugins->isExisting("image")): ?>
<?php list($src, $width, $height) = $yellow->plugins->get("image")->getImageInfo($file->fileName, $size, $size); ?>
<?php endif ?>
<li><a href="<?php echo htmlspecialchars($file->getLocation()) ?>"><img src="<?php echo htmlspecialchars($src) ?>" width="<?php echo htmlspecialchars($width) ?>" height="<?php echo htmlspecialchars($height) ?>" alt="<?php echo htmlspecialchars(basename($file->getLocation())) ?>" title="<?php echo htmlspecialchars(basename($file->getLocation())) ?>" /></a></li>
<?php endforeach ?>
</ul>
