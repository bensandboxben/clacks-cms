<?php
/**
* Shared <head> assets for every CMS page (main pages and popup windows):
* viewport, stylesheet, theme initialisation and interface script.
* The inline script runs before first paint so the saved light/dark theme
* and sidebar state are applied without a flash.
* @package ClacksWeb v2 CMS
*/
include_once __DIR__ . "/ui.inc.php";
?>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="color-scheme" content="light dark" />
<link rel="stylesheet" href="<?php echo cms_asset('css/app.css'); ?>" type="text/css" />
<script>
(function () {
	var d = document.documentElement, t = null, s = null;
	try { t = localStorage.getItem('cms-theme'); s = localStorage.getItem('cms-sidebar'); } catch (e) {}
	if (t !== 'light' && t !== 'dark') {
		t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
	}
	if (t === 'dark') d.classList.add('dark');
	if (s === 'collapsed') d.classList.add('sidebar-collapsed');
	if (!d.getAttribute('lang')) d.setAttribute('lang', 'en-GB');
	d.classList.add('js');
})();
</script>
<script src="<?php echo cms_asset('js/cms.js'); ?>" defer></script>
