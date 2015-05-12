<div class="content main">
<div class="entry">
<div class="entry-header"><h1><?php echo $yellow->page->getHtml("titleContent") ?></h1></div>
<div class="entry-meta"><?php echo htmlspecialchars($yellow->page->getDate("published")) ?> <?php echo $yellow->text->getHtml("blogBy") ?> <?php $authorCounter = 0; foreach(preg_split("/,\s*/", $yellow->page->get("author")) as $author) { if(++$authorCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getParentTop()->getLocation().$yellow->toolbox->normaliseArgs("author:$author")."\">".htmlspecialchars($author)."</a>"; } ?></div>
<div class="entry-content"><?php echo $yellow->page->getContent() ?></div>
<div class="entry-footer">
<?php if($yellow->page->isExisting("tag")): ?>
<p><?php echo $yellow->text->getHtml("blogTag") ?> <?php $tagCounter = 0; foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { if(++$tagCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getParentTop()->getLocation().$yellow->toolbox->normaliseArgs("tag:$tag")."\">".htmlspecialchars($tag)."</a>"; } ?></p>
<?php endif ?>
</div>
</div>
<?php echo $yellow->page->getExtra() ?>
</div>
