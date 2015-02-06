<?php $yellow->snippet("header") ?>
<?php $yellow->snippet("sitename") ?>
<?php $yellow->snippet("navigation") ?>
<div class="content">
<h1><?php echo $yellow->page->getHtml("titleContent") ?></h1>
<?php echo $yellow->page->getContent() ?>
<?php $yellow->snippet("pagelist", $yellow->page->getChildren(), "themes", "25%") ?>
</div>
<?php $yellow->snippet("footer") ?>