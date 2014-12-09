<div class="language">
<ul>
<?php foreach($yellow->pages->translation($yellow->page->getLocation(), true, !$yellow->page->isVisible()) as $page): ?>
<li><a href="<?php echo $page->getLocation().$yellow->toolbox->getLocationArgs() ?>"><?php echo $yellow->text->getTextHtml("languageDescription", $page->get("language")) ?></a></li>
<?php endforeach ?>
</ul>
</div>
