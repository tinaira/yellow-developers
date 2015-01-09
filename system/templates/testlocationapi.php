<?php $pages = $yellow->pages->index(true, true) ?>
<?php $yellow->snippet("header") ?>
<?php $yellow->snippet("sitename") ?>
<?php $yellow->snippet("navigation") ?>
<div class="content">
<h1><?php echo $yellow->page->getHtml("title") ?></h1>
<?php echo $yellow->page->getContent() ?>
<table>
<tr><th>Location</th><th>Parent</th><th>Children</th><th>Visible</th><th>Siblings</th><th>Navigation</th><th>Modified</th></tr>
<?php foreach($pages as $page): ?>
<tr>
<td><a href="<?php echo $page->getLocation() ?>"><?php echo $page->getLocation() ?></a></td>
<td><?php echo $page->getParent()!=NULL				? "yes" : "<span style=\"color:red\">no</span>" ?></td>
<td><?php echo count($page->getChildren(true))!=0	? "yes" : "<span style=\"color:red\">no</span>" ?></td>
<td><?php echo $page->isVisible()					? "yes" : "<span style=\"color:red\">no</span>" ?></td>
<td><?php echo count($page->getSiblings(true)) ?></td>
<td><?php echo $page->getParent()!=NULL ? $page->getParent()->getHtml("title") : $page->getHtml("titleNavigation") ?></td>
<td><?php $date = date("Y-m-d", $page->getModified()); echo $date!=date("Y-m-d") ? $date : "Today" ?></td>
</tr>
<?php endforeach ?>
</table>
</div>
<?php $yellow->snippet("footer") ?>
<?php $yellow->page->header("Last-Modified: ".$pages->getModified(true)) ?>
<?php $yellow->page->header("Cache-Control: max-age=60") ?>