<div class="navigationsearch" ?>
<form class="search-form" action="<?php echo $yellow->page->base ?>/search/" method="post">
<input class="search-text" type="text" name="query" placeholder="<?php echo $yellow->text->getHtml("searchButton") ?>" />
<?php if($yellow->plugins->isExisting("fontawesome")):?>
<button class="search-button" type="submit"><i class="fa fa-search"></i></button>
<?php endif ?>
<input type="hidden" name="clean-url" />
</form>
</div>
