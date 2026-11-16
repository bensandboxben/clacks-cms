<?php
/**
* Closes the application shell opened by includes/header.inc.php
* @package ClacksWeb v2 CMS
*/
if (isset($cms_shell) && $cms_shell == 'auth') {
?>
	</main>
	<footer class="px-4 pb-6 text-center text-xs text-fg-subtle sm:px-8">
		&copy; Copyright <?php echo date("Y"); ?> Clackmannanshire Council
	</footer>
</div>
<?php } else { ?>
	</main>
	<footer class="mx-auto flex w-full max-w-content flex-col gap-2 border-t border-line px-4 py-5 text-xs text-fg-subtle sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
		<p class="m-0">&copy; Copyright <?php echo date("Y"); ?> Clackmannanshire Council</p>
		<ul class="m-0 flex list-none flex-wrap gap-x-4 gap-y-1 p-0">
			<li><a class="text-fg-subtle no-underline hover:text-fg hover:underline" href="index.php">Home</a></li>
			<li><a class="text-fg-subtle no-underline hover:text-fg hover:underline" href="http://connect/">CONNECT</a></li>
			<li><a class="text-fg-subtle no-underline hover:text-fg hover:underline" href="http://www.clacksweb.org.uk/">Live Site</a></li>
		</ul>
	</footer>
</div>
<?php } ?>
</body>
</html>
