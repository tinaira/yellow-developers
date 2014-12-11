<?php list($name, $pattern) = $yellow->getSnippetArgs() ?>
<?php if(!$pattern) $pattern = basename($yellow->page->fileName).".(jpg|png)" ?>
<div class="imagelist">
<ul>
<?php $path = dirname($pattern); $path = ($path!=".") ? $path."/" : ""; $regex = "/^".basename($pattern)."$/"; ?>
<?php foreach($yellow->toolbox->getDirectoryEntries($yellow->config->get("imageDir").$path, $regex, true, false, false) as $fileName): ?>
<?php list($width, $height) = $yellow->toolbox->detectImageInfo($yellow->config->get("imageDir").$path.$fileName); ?>
<?php $src = htmlspecialchars($yellow->config->get("serverBase").$yellow->config->get("imageLocation").$path.$fileName); ?>
<li><a href="<?php echo $src ?>"><img src="<?php echo $src ?>" width="<?php echo $width ?>" height="<?php echo $height ?>" alt="Image" /></a></li>
<?php endforeach ?>
</ul>
</div>
