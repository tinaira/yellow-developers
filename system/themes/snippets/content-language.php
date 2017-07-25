<div class="content">
<?php $yellow->snippet("sidebar") ?>
<div class="main">
<h1><?php echo $yellow->page->getHtml("titleContent") ?></h1>
<div class="language">
<?php foreach($yellow->pages->multi("/")->sort("language") as $page): ?>
<?php $language = $page->get("language") ?>
<p><a href="<?php echo $page->getLocation(true) ?>"><img src="<?php echo "/media/images/language-$language.png" ?>" width="48" height="48" alt="<?php echo $yellow->text->getTextHtml("languageDescription", $language) ?>" title="<?php echo $yellow->text->getTextHtml("languageDescription", $language) ?>" /><?php echo $yellow->text->getTextHtml("languageDescription", $language) ?></a></p>
<?php endforeach ?>
</div>
</div>
</div>
