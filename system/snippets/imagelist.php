<?php list($name, $pattern, $style, $size) = $yellow->getSnippetArgs() ?>
<?php if(!$pattern) $pattern = basename($yellow->page->location).".(jpg|png)" ?>
<?php if(!$style) $style = "imagelist" ?>
<?php if(!$size) $size = "100%" ?>
<ul class="<?php echo htmlspecialchars($style) ?>">
<?php $path = dirname($pattern); $path = ($path!=".") ? $path."/" : ""; $regex = "/^".basename($pattern)."$/"; ?>
<?php foreach($yellow->toolbox->getDirectoryEntries($yellow->config->get("imageDir").$path, $regex, true, false, false) as $fileName): ?>
<?php $location = $src = $yellow->config->get("serverBase").$yellow->config->get("imageLocation").$path.$fileName; ?>
<?php if($yellow->plugins->isExisting("image")): ?>
<?php list($src, $width, $height) = $yellow->plugins->get("image")->getImageInfo($path.$fileName, $size, $size); ?>
<?php endif ?>
<li><a href="<?php echo htmlspecialchars($location) ?>"><img src="<?php echo htmlspecialchars($src) ?>" width="<?php echo htmlspecialchars($width) ?>" height="<?php echo htmlspecialchars($height) ?>" alt="<?php echo htmlspecialchars($fileName) ?>" title="<?php echo htmlspecialchars($fileName) ?>" /></a></li>
<?php endforeach ?>
</ul>
