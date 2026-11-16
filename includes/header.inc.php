<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
/**
* Application shell: document head, sidebar navigation and top bar.
* Closed by includes/footer.inc.php.
* @package ClacksWeb v2 CMS
*/
header('Content-Type: text/html; charset=iso-8859-1');
include_once __DIR__ . "/ui.inc.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$cms_logged_in = isset($_SESSION["username"]);
if ($cms_logged_in) {
	$useraccess = $db->get_row("SELECT * FROM cms.useraccess WHERE userid='$_SESSION[userid]'");
}
$cms_page = cms_current_page();
$cms_parents = cms_page_parents();
$cms_active_area = isset($cms_parents[$cms_page]) ? $cms_parents[$cms_page] : $cms_page;
// Pages which render their own heading (matches the previous header behaviour)
$cms_hide_title = in_array($cms_page, array('index', 'login', 'profile'));
$cms_page_title = isset($CMStitle) ? $CMStitle : 'CMS';
if ($cms_page == 'index') $cms_page_title = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="iso-8859-1" />
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<title><?php echo strip_tags($cms_page_title); ?> &middot; ClacksWeb CMS</title>
<?php include __DIR__ . "/head.inc.php"; ?>
</head>
<?php if (!$cms_logged_in) { $cms_shell = 'auth'; ?>
<body class="bg-canvas">
<a class="skip-link" href="#main">Skip to main content</a>
<div class="flex min-h-screen flex-col">
	<header class="flex items-center justify-between px-4 py-4 sm:px-8">
		<a href="index.php" class="flex items-center gap-3 text-fg no-underline">
			<span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-primary-fg"><?php echo cms_icon('landmark', 'icon-lg'); ?></span>
			<span class="leading-tight"><span class="block text-sm font-semibold">ClacksWeb CMS</span><span class="block text-xs text-fg-muted">Clackmannanshire Council</span></span>
		</a>
		<button type="button" class="btn btn-ghost btn-icon" data-theme-toggle aria-label="Toggle dark mode" aria-pressed="false">
			<?php echo cms_icon('moon', 'theme-icon-dark dark:hidden'); ?><?php echo cms_icon('sun', 'theme-icon-light hidden dark:inline-block'); ?>
		</button>
	</header>
	<main id="main" class="flex flex-1 items-start justify-center px-4 pb-16 pt-6 sm:pt-16" tabindex="-1">
<?php } else { $cms_shell = 'app'; ?>
<body>
<a class="skip-link" href="#main">Skip to main content</a>
<div class="app-backdrop" data-nav-close></div>

<aside class="app-sidebar" id="app-sidebar" aria-label="CMS navigation">
	<div class="sidebar-brand flex h-14 shrink-0 items-center gap-3 border-b border-line px-4">
		<a href="index.php" class="flex min-w-0 items-center gap-3 text-fg no-underline" title="ClacksWeb CMS home">
			<span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary text-primary-fg"><?php echo cms_icon('landmark'); ?></span>
			<span class="sidebar-brand-text min-w-0 leading-tight">
				<span class="block truncate text-sm font-semibold">ClacksWeb CMS</span>
				<span class="block truncate text-2xs text-fg-subtle">Clackmannanshire Council</span>
			</span>
		</a>
		<button type="button" class="btn btn-ghost btn-icon btn-sm ml-auto lg:hidden" data-nav-close aria-label="Close navigation"><?php echo cms_icon('x'); ?></button>
	</div>

	<div class="nav-filter shrink-0 px-3 pb-1 pt-3">
		<label for="nav-filter-input" class="sr-only">Find a CMS area</label>
		<div class="relative">
			<?php echo cms_icon('search', 'pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-fg-subtle'); ?>
			<input type="search" id="nav-filter-input" class="w-full !pl-8 !text-[0.8125rem]" placeholder="Jump to&hellip;" autocomplete="off" data-nav-filter />
			<kbd class="pointer-events-none absolute right-2 top-1/2 hidden -translate-y-1/2 rounded border border-line bg-surface-2 px-1.5 text-2xs text-fg-subtle sm:block">/</kbd>
		</div>
	</div>

	<nav class="min-h-0 flex-1 overflow-y-auto px-3 pb-4 pt-2" aria-label="Main">
		<ul class="m-0 list-none p-0">
			<li><a class="nav-item" href="index.php" title="Dashboard"<?php if ($cms_page == 'index') echo ' aria-current="page"'; ?>><?php echo cms_icon('layout-dashboard'); ?><span class="nav-label">Dashboard</span></a></li>
		</ul>
<?php
// Build the list of areas this user can access (same rule as before: useraccess->area == 1)
$cms_user_areas = array();
foreach ($areas as $area => $title) {
	if (is_object($useraccess) && property_exists($useraccess, $area) && $useraccess->$area == 1) {
		$cms_user_areas[$area] = $title;
	}
}
$cms_groups = cms_nav_groups();
$cms_grouped = array();
foreach ($cms_groups as $gkey => $group) {
	foreach ($group['areas'] as $area) {
		if (isset($cms_user_areas[$area])) $cms_grouped[$gkey][$area] = $cms_user_areas[$area];
	}
}
$cms_other = array_diff_key($cms_user_areas, call_user_func_array('array_merge', array_map(function ($g) { return array_flip($g['areas']); }, array_values($cms_groups))));
if (count($cms_other) > 0) {
	$cms_groups['other'] = array('title' => 'Other', 'icon' => 'layers', 'areas' => array_keys($cms_other));
	$cms_grouped['other'] = $cms_other;
}
if (count($cms_user_areas) > 0) echo '<p class="nav-section-title">Manage</p>';
echo '<ul class="m-0 list-none p-0">';
foreach ($cms_groups as $gkey => $group) {
	if (empty($cms_grouped[$gkey])) continue;
	// keep the defined order within the group but sort alphabetically like before
	$items = $cms_grouped[$gkey];
	asort($items);
	$is_active_group = array_key_exists($cms_active_area, $items);
	echo '<li class="nav-group-item" data-nav-group="' . $gkey . '"><details class="nav-group" data-group="' . $gkey . '"' . ($is_active_group ? ' open data-active' : '') . '>';
	echo '<summary title="' . htmlspecialchars($group['title']) . '">' . cms_icon($group['icon']) . '<span class="nav-label">' . htmlspecialchars($group['title']) . '</span>' . cms_icon('chevron-right', 'nav-group-chevron') . '</summary>';
	echo '<ul class="nav-sub">';
	foreach ($items as $area => $title) {
		$current = ($area == $cms_active_area) ? ' aria-current="page"' : '';
		$plain = strip_tags(html_entity_decode($title, ENT_QUOTES, 'ISO-8859-1'));
		if (array_key_exists($area, $popups)) {
			$dim = explode(",", $popups[$area]);
			echo '<li><a class="nav-item" href="' . $area . '.php" data-popup-width="' . $dim[0] . '" data-popup-height="' . $dim[1] . '" data-keywords="' . htmlspecialchars(strtolower($plain . ' ' . $area)) . '"' . $current . '>' . cms_icon(cms_area_icon($area)) . '<span class="nav-label">' . $title . '</span><span class="sr-only"> (opens in a new window)</span></a></li>';
		} else {
			echo '<li><a class="nav-item" href="' . $area . '.php" data-keywords="' . htmlspecialchars(strtolower($plain . ' ' . $area)) . '"' . $current . '>' . cms_icon(cms_area_icon($area)) . '<span class="nav-label">' . $title . '</span></a></li>';
		}
	}
	echo '</ul></details></li>';
}
echo '</ul>';
?>
		<p class="nav-filter-empty hidden px-3 py-2 text-sm text-fg-muted" data-nav-empty>No matching areas</p>
	</nav>

	<div class="shrink-0 border-t border-line p-3">
		<ul class="m-0 list-none p-0">
			<li><a class="nav-item" href="http://www.clacksweb.org.uk/" title="Live site"><?php echo cms_icon('globe'); ?><span class="nav-label">Live site</span></a></li>
			<li><a class="nav-item" href="http://connect/" title="CONNECT"><?php echo cms_icon('external-link'); ?><span class="nav-label">CONNECT</span></a></li>
			<li class="hidden lg:block"><button type="button" class="nav-item sidebar-collapse-btn border-0 bg-transparent" data-sidebar-toggle aria-controls="app-sidebar" aria-expanded="true" title="Collapse sidebar"><?php echo cms_icon('panel-left-close', 'icon-collapse'); ?><?php echo cms_icon('panel-left-open', 'icon-expand'); ?><span class="nav-label">Collapse sidebar</span></button></li>
		</ul>
	</div>
</aside>

<div class="app-main">
	<header class="topbar">
		<button type="button" class="btn btn-ghost btn-icon -ml-2 lg:hidden" data-nav-open aria-controls="app-sidebar" aria-expanded="false" aria-label="Open navigation"><?php echo cms_icon('menu', 'icon-lg'); ?></button>
		<nav aria-label="Breadcrumb" class="min-w-0 flex-1">
			<ol class="breadcrumbs">
<?php if ($cms_page == 'index') { ?>
				<li><span aria-current="page">Dashboard</span></li>
<?php } else { ?>
				<li class="hidden sm:flex"><a href="index.php">Dashboard</a><?php echo cms_icon('chevron-right', 'icon-sm text-fg-subtle'); ?></li>
<?php
	if ($cms_active_area != $cms_page && file_exists(dirname(__DIR__) . '/' . $cms_active_area . '.php')) {
		$parent_title = isset($areas[$cms_active_area]) ? $areas[$cms_active_area] : ucfirst($cms_active_area);
		echo '<li class="hidden sm:flex"><a href="' . $cms_active_area . '.php">' . $parent_title . '</a>' . cms_icon('chevron-right', 'icon-sm text-fg-subtle') . '</li>';
	}
?>
				<li class="min-w-0"><span aria-current="page"><?php echo $cms_page_title; ?></span></li>
<?php } ?>
			</ol>
		</nav>

		<button type="button" class="btn btn-ghost btn-icon" data-theme-toggle aria-label="Toggle dark mode" aria-pressed="false" title="Toggle dark mode">
			<?php echo cms_icon('moon', 'theme-icon-dark dark:hidden'); ?><?php echo cms_icon('sun', 'theme-icon-light hidden dark:inline-block'); ?>
		</button>

		<div class="relative" data-menu>
			<button type="button" class="btn btn-ghost gap-2 px-1.5 sm:px-2" aria-haspopup="true" aria-expanded="false" aria-controls="user-menu" data-menu-button>
				<span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-soft text-xs font-semibold uppercase text-primary-text" aria-hidden="true"><?php echo htmlspecialchars(substr($_SESSION["username"], 0, 1)); ?></span>
				<span class="hidden max-w-[10rem] truncate text-sm font-medium sm:inline"><?php echo $_SESSION["username"]; ?></span>
				<span class="sr-only sm:hidden">Account menu for <?php echo $_SESSION["username"]; ?></span>
				<?php echo cms_icon('chevron-down', 'icon-sm text-fg-subtle'); ?>
			</button>
			<div class="menu-panel" id="user-menu" hidden data-menu-panel>
				<div class="flex items-center gap-2 border-b border-line px-2.5 pb-2.5 pt-1.5">
					<span class="h-2 w-2 rounded-full bg-success" aria-hidden="true"></span>
					<span class="min-w-0 truncate text-sm"><span class="text-fg-muted">Signed in as</span> <strong><?php echo $_SESSION["username"]; ?></strong></span>
				</div>
				<div class="pt-1.5">
					<a class="menu-item" href="profile.php"><?php echo cms_icon('user-round'); ?>Edit profile</a>
					<a class="menu-item" href="index.php"><?php echo cms_icon('house'); ?>Home</a>
					<a class="menu-item" href="login.php?logout=1"><?php echo cms_icon('log-out'); ?>Logout</a>
				</div>
			</div>
		</div>
	</header>

	<main id="main" class="app-content mx-auto w-full max-w-content flex-1 px-4 py-6 sm:px-6 lg:px-8" tabindex="-1">
<?php if (!$cms_hide_title) { ?>
		<div class="page-header">
			<div class="min-w-0">
				<h1 class="page-title"><?php echo $cms_page_title; ?></h1>
			</div>
		</div>
<?php } ?>
<?php } ?>
