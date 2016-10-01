<div class="footer">
<div class="siteinfo">
<div class="siteinfo-left">
Yellow is a flat-file CMS and a static site generator.
</div>
<div class="siteinfo-right">
<a href="<?php echo $yellow->text->get("yellowUrl") ?>">Made with Yellow</a>.
<a href="<?php echo $yellow->page->base."/help/status" ?>">Status</a>.
<a href="<?php echo $yellow->page->get("pageEdit") ?>">Edit</a>.
</div>
</div>
<div class="siteinfo-banner"></div>
</div>
</div>
<?php echo $yellow->page->getExtra("footer") ?>
</body>
</html>