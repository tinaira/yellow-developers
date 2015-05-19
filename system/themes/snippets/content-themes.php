<div class="content main">
<h1><?php echo $yellow->page->getHtml("titleContent") ?></h1>
<?php echo $yellow->page->getContent() ?>
<?php $yellow->snippet("pagelist", $yellow->page->getChildren(), "themes", "25%") ?>
</div>
