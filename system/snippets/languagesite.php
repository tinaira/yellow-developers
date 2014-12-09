<div class="languagesite">
<ul>
<?php foreach($yellow->pages->translation("/") as $page): ?>
<li><a href="<?php echo $page->getLocation() ?>"><?php echo $yellow->text->getTextHtml("languageDescription", $page->get("language")) ?></a></li>
<?php endforeach ?>
</ul>
</div>
