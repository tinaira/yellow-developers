<div class="footer">
<div class="siteinfo">
<div class="siteinfo-left">
<a href="<?php echo $yellow->page->base."/" ?>"><?php echo $yellow->page->getHtml("sitename") ?></a> &nbsp;
<a href="<?php echo $yellow->page->base."/sitemap/" ?>">Sitemap</a> &nbsp;
<a href="<?php echo $yellow->page->base."/help/status" ?>">Status</a> &nbsp;
</div>
<div class="siteinfo-right">
<a href="<?php echo $yellow->page->base."/help/support" ?>">Support</a> &nbsp;
<a href="<?php echo $yellow->page->base."/help/yellow-api" ?>">API</a> &nbsp;
<a href="<?php echo $yellow->text->get("yellowUrl") ?>">Made with Yellow</a>
</div>
<div class="siteinfo-banner"></div>
</div>
</div>
<?php echo $yellow->page->getExtra("footer") ?>
</body>
</html>