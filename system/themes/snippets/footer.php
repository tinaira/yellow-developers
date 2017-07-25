<div class="footer">
<div class="siteinfo">
<div class="siteinfo-left">
<a href="https://datenstrom.se<?php echo $yellow->pages->getHomeLocation($yellow->page->location) ?>">&copy; 2017 Datenstrom</a> &nbsp;
</div>
<div class="siteinfo-right">
<a href="<?php echo $yellow->page->getBase(true)."/help/support" ?>">Support</a> &nbsp;
<a class="language" href="<?php echo $yellow->page->getBase()."/language/" ?>"><img src="<?php echo "/media/images/language-".$yellow->text->language.".png"?>" width="20" height="20" alt="<?php echo $yellow->text->getHtml("languageDescription") ?>" title="<?php echo $yellow->text->getHtml("languageDescription") ?>"><?php echo $yellow->text->getHtml("languageDescription") ?></a>
</div>
<div class="siteinfo-banner"></div>
</div>
</div>
</div>
<?php echo $yellow->page->getExtra("footer") ?>
</body>
</html>
