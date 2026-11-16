<?php
/**
* Presentation helpers for the ClacksWeb CMS interface.
* These functions only produce markup; they do not touch the database,
* session or request data beyond reading the current script name.
* @package ClacksWeb v2 CMS
*/

if (!function_exists('cms_icon')) {

/**
* Return an inline SVG referencing a Lucide icon in images/icons.svg
* @param string $name   icon id (see tools/build-icons.mjs)
* @param string $class  extra CSS classes
* @param string $label  accessible label; if empty the icon is decorative
* @return string
*/
function cms_icon($name, $class = '', $label = '') {
	$cls = trim('icon ' . $class);
	$aria = $label !== '' ? ' role="img" aria-label="' . htmlspecialchars($label, ENT_QUOTES, 'ISO-8859-1') . '"' : ' aria-hidden="true"';
	return '<svg class="' . $cls . '"' . $aria . ' focusable="false"><use href="images/icons.svg#' . $name . '"></use></svg>';
}

/**
* Return an asset URL with a cache-busting version based on file modification time
* @param string $path path relative to the CMS root
* @return string
*/
function cms_asset($path) {
	$file = dirname(__DIR__) . '/' . $path;
	$v = @filemtime($file);
	return $path . ($v ? '?v=' . $v : '');
}

/**
* Current script name without extension, e.g. "press"
* @return string
*/
function cms_current_page() {
	return basename($_SERVER['PHP_SELF'], '.php');
}

/**
* Navigation groups for the sidebar. Every key of $areas (includes/constants.inc.php)
* is shown; areas not listed here appear under "Other" so nothing is ever hidden.
* @return array
*/
function cms_nav_groups() {
	return array(
		'website' => array('title' => 'Website', 'icon' => 'globe', 'areas' => array('content', 'intranetcontent', 'fp', 'mainfeature', 'fpalert', 'contacts', 'forms', 'docs', 'extlinks', 'rclinks', 'rclinkcats', 'galleries', 'polls', 'slgcl')),
		'democracy' => array('title' => 'Council & democracy', 'icon' => 'landmark', 'areas' => array('councillordetails', 'councillordocs', 'members', 'meetings', 'calendar', 'bodies', 'elections', 'consultations')),
		'news' => array('title' => 'News & notices', 'icon' => 'megaphone', 'areas' => array('press', 'events', 'emergencynotices', 'worknotices', 'roadreport', 'commsqs')),
		'licensing' => array('title' => 'Licensing & regulation', 'icon' => 'scale', 'areas' => array('licenses', 'occasionallicences', 'hygieneinspections', 'hicategories', 'hiestablishments', 'collections', 'tros')),
		'services' => array('title' => 'Services & directories', 'icon' => 'building-2', 'areas' => array('busdir', 'schools', 'schoolsession', 'pows', 'tenders')),
		'jobs' => array('title' => 'Jobs', 'icon' => 'briefcase', 'areas' => array('vacancies', 'jobpacks')),
	);
}

/**
* Icon for a CMS area
* @param string $area
* @return string icon name
*/
function cms_area_icon($area) {
	$icons = array(
		'content' => 'file-text', 'intranetcontent' => 'monitor', 'fp' => 'panels-top-left', 'mainfeature' => 'star',
		'fpalert' => 'bell', 'contacts' => 'contact', 'forms' => 'clipboard-list', 'docs' => 'files',
		'extlinks' => 'external-link', 'rclinks' => 'library', 'rclinkcats' => 'tags', 'galleries' => 'images',
		'polls' => 'chart-bar', 'slgcl' => 'list-tree', 'councillordetails' => 'user-round-pen', 'councillordocs' => 'folder-open',
		'members' => 'users', 'meetings' => 'notebook-tabs', 'calendar' => 'calendar-days', 'bodies' => 'building-2',
		'elections' => 'vote', 'consultations' => 'list-checks', 'press' => 'newspaper', 'events' => 'calendar',
		'emergencynotices' => 'siren', 'worknotices' => 'hard-hat', 'roadreport' => 'traffic-cone', 'commsqs' => 'clipboard-list',
		'licenses' => 'scale', 'occasionallicences' => 'ticket', 'hygieneinspections' => 'utensils', 'hicategories' => 'tags',
		'hiestablishments' => 'store', 'collections' => 'heart-handshake', 'tros' => 'construction', 'busdir' => 'store',
		'schools' => 'school', 'schoolsession' => 'graduation-cap', 'pows' => 'church', 'tenders' => 'file-pen',
		'vacancies' => 'briefcase', 'jobpacks' => 'paperclip',
	);
	return isset($icons[$area]) ? $icons[$area] : 'folder';
}

/**
* Pages that belong to a parent area for navigation highlighting / breadcrumbs
* @return array page => area
*/
function cms_page_parents() {
	return array(
		'newcontent' => 'content',
		'newintranetcontent' => 'intranetcontent',
		'newcitydealcontent' => 'citydeal',
		'menus' => 'extlinks',
	);
}

/**
* Output the standard list-page heading with an optional primary action.
* Used by pages that render their own heading.
* @param string $title
* @param string $description
*/
function cms_page_heading($title, $description = '') {
	echo '<div class="page-header"><div><h1 class="page-title">' . $title . '</h1>';
	if ($description) echo '<p class="page-description">' . $description . '</p>';
	echo '</div></div>';
}

/**
* Filter / pagination toolbar for list pages (used by print_nav and print_calendar_nav).
* Emits exactly the same form fields and link parameters as the original
* table#dbnav markup: GET form named "filter" with category (or doctype),
* filtertxt, gopage, submitpage and srchtxt; previous/next links use ?offset=.
*/
function cms_list_toolbar($table, $currentpage, $pages, $resultsnumber, $prevoffset, $nextoffset, $categories, $filtertxt, $selectname, $filterlabel) {
	$self = $_SERVER["PHP_SELF"];
	$total = (int)$resultsnumber;
	echo '<form method="get" action="" name="filter" class="list-toolbar" role="search" aria-label="Filter records">';
	echo '<div class="list-toolbar-filters">';
	echo '<div class="list-toolbar-search">' . cms_icon('search');
	echo '<label class="sr-only" for="filtertxt-' . $table . '">Filter records</label>';
	echo '<input type="text" width="15" name="filtertxt" id="filtertxt-' . $table . '" placeholder="Filter records&hellip;"';
	if ($filtertxt) echo ' value="' . $filtertxt . '"';
	echo '></div>';
	if (is_array($categories) && count($categories) > 0) {
		echo '<label class="sr-only" for="' . $selectname . '-' . $table . '">' . ($selectname == 'doctype' ? 'Document type' : 'Category') . '</label>';
		echo '<select name="' . $selectname . '" id="' . $selectname . '-' . $table . '" class="max-w-[16rem]">';
		echo '<option value="" >All</option>';
		foreach ($categories as $category) {
			echo '<option value="' . $category[0] . '"';
			if (get_category($table) == $category[0]) echo ' selected="selected"';
			echo '>' . $category[1] . '</option>';
		}
		echo '</select> ';
	}
	echo '<button class="btn btn-md btn-secondary" type="submit" value="' . $filterlabel . '">' . cms_icon('filter') . '<span>' . $filterlabel . '</span></button>';
	if ($filtertxt) echo '<input type="hidden" name="srchtxt" value="' . $filtertxt . '">';
	echo '</div>';

	echo '<div class="pager">';
	echo '<span class="pager-status" aria-live="polite">';
	if ($total > 0) {
		echo 'Page <strong class="text-fg">' . $currentpage . '</strong> of <strong class="text-fg">' . $pages . '</strong> <span class="text-fg-subtle">&middot; ' . number_format($total) . ' record' . ($total == 1 ? '' : 's') . '</span>';
	} else {
		echo 'No records';
	}
	echo '</span>';
	echo '<span class="inline-flex">';
	if ($prevoffset !== null) {
		echo '<a class="btn btn-sm btn-secondary btn-icon rounded-r-none" href="' . $self . '?offset=' . $prevoffset . '" title="Previous page">' . cms_icon('chevron-left', '', 'Previous page') . '</a>';
	} else {
		echo '<span class="btn btn-sm btn-secondary btn-icon rounded-r-none" aria-disabled="true">' . cms_icon('chevron-left', '', 'Previous page (unavailable)') . '</span>';
	}
	if ($nextoffset !== null) {
		echo '<a class="btn btn-sm btn-secondary btn-icon -ml-px rounded-l-none" href="' . $self . '?offset=' . $nextoffset . '" title="Next page">' . cms_icon('chevron-right', '', 'Next page') . '</a>';
	} else {
		echo '<span class="btn btn-sm btn-secondary btn-icon -ml-px rounded-l-none" aria-disabled="true">' . cms_icon('chevron-right', '', 'Next page (unavailable)') . '</span>';
	}
	echo '</span>';
	echo '<span class="pager-goto"><label for="gopage-' . $table . '">Go to page</label>';
	echo '<input type="text" size="2" maxlength="4" name="gopage" id="gopage-' . $table . '" inputmode="numeric">';
	echo '<input class="btn btn-sm btn-secondary" type="submit" value="Go" name="submitpage" /></span>';
	echo '</div>';
	echo '</form>';
}

/**
* Render an alert box
* @param string $type success|warning|danger|info
* @param string $html message markup
* @return string
*/
function cms_alert($type, $html) {
	$icons = array('success' => 'circle-check', 'warning' => 'triangle-alert', 'danger' => 'circle-alert', 'info' => 'info');
	$role = ($type == 'danger' || $type == 'warning') ? 'alert' : 'status';
	return '<div class="alert alert-' . $type . '" role="' . $role . '">' . cms_icon(isset($icons[$type]) ? $icons[$type] : 'info') . '<div class="min-w-0 flex-1">' . $html . '</div></div>';
}

}
