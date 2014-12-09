<?php $pages = $yellow->pages->create()->append($yellow->page); ?>
<?php if($yellow->toolbox->isLocationArgs($this->toolbox->getLocation())): ?>
<?php $pages->merge($yellow->page->getChildren(!$yellow->page->isVisible())) ?>
<?php $pagesFilter = array() ?>
<?php if($_REQUEST["special"] == "changes") { $chronologicalOrder = true; array_push($pagesFilter, $yellow->text->get("wikiSpecialChanges")); } ?>
<?php if($_REQUEST["tag"]) { $pages->filter("tag", $_REQUEST["tag"]); array_push($pagesFilter, $pages->getFilter()); } ?>
<?php if($_REQUEST["title"]) { $pages->filter("title", $_REQUEST["title"], false); array_push($pagesFilter, $pages->getFilter()); } ?>
<?php if($_REQUEST["modified"]) { $pages->filter("modified", $_REQUEST["modified"], false); array_push($pagesFilter, $pages->getFilter()); } ?>
<?php if(!empty($pagesFilter)): ?>
<?php $pages->sort($chronologicalOrder ? "modified" : "title", $chronologicalOrder)->pagination(30) ?>
<?php if($pages->getPaginationPage() > $pages->getPaginationCount()) $yellow->page->error(404) ?>
<?php $title = implode(' ', $pagesFilter) ?>
<?php $yellow->page->set("titleHeader", $title." - ".$yellow->page->get("sitename")) ?>
<?php $yellow->page->set("titleWiki", $yellow->text->get("wikiFilter")." ".$title) ?>
<?php endif ?>
<?php endif ?>
<?php $yellow->snippet("header") ?>
<?php $yellow->snippet("sitename") ?>
<?php $yellow->snippet("navigation") ?>
<?php if(!empty($pagesFilter)): ?>
<div class="content wikipages">
<h1><?php echo $yellow->page->getHtml("titleWiki") ?></h1>
<ul>
<?php foreach($pages as $page): ?>
<?php if($chronologicalOrder): ?>
<?php $sectionNew = htmlspecialchars(date("Y-m-d", $page->getModified())) ?>
<?php else: ?>
<?php $sectionNew = htmlspecialchars(strtoupperu(substru($page->get("title"), 0, 1))) ?>
<?php endif ?>
<?php if($section != $sectionNew) { $section = $sectionNew; echo "</ul><h2>$section</h2><ul>\r\n"; } ?>
<li><a href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("title") ?></a></li>
<?php endforeach ?>
</ul>
<?php $yellow->snippet("pagination", $pages) ?>
</div>
<?php else: ?>
<div class="content wiki">
<div class="entry">
<div class="entry-header"><h1><?php echo $yellow->page->getHtml("title") ?></h1></div>
<div class="entry-content"><?php echo $yellow->page->getContent() ?></div>
<div class="entry-footer">
<?php if($yellow->page->isExisting("tag")): ?>
<p><?php echo $yellow->text->getHtml("wikiTag") ?> <?php $tagCounter = 0; foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { if(++$tagCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getParentTop()->getLocation().$yellow->toolbox->normaliseArgs("tag:$tag")."\">".htmlspecialchars($tag)."</a>"; } ?></p>
<?php endif ?>
</div>
</div>
</div>
<?php endif ?>
<?php $yellow->snippet("footer") ?>
<?php $yellow->page->header("Last-Modified: ".$pages->getModified(true)) ?>