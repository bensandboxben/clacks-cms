<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


session_start(); // Start the session

$areas = [];

/**
* Login to ClacksWeb CMS
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "CMS";
include "includes/header.inc.php";
reset($areas);

// Areas this user can manage (unchanged rule: useraccess->area == 1)
$dash_areas = array();
foreach ($areas as $area=>$title) {
	if ($useraccess->$area == 1) {
		$dash_areas[$area] = $title;
	}
}
?>

<div class="page-header">
	<div class="min-w-0">
		<h1 class="page-title">Welcome, <?php echo $_SESSION["username"]; ?></h1>
		<p class="page-description">Manage content and services for the Clackmannanshire Council website.</p>
	</div>
<?php if (isset($dash_areas["content"])) { ?>
	<div class="flex flex-wrap gap-2">
		<a class="btn btn-secondary" href="content.php"><?php echo cms_icon('file-text'); ?>All content</a>
		<a class="btn btn-primary" href="newcontent.php"><?php echo cms_icon('plus'); ?>New page</a>
	</div>
<?php } ?>
</div>

<div class="grid gap-6 xl:grid-cols-3">
	<div class="flex min-w-0 flex-col gap-6 xl:col-span-2">

		<section class="card" aria-labelledby="dash-content-heading">
			<div class="card-header">
				<h2 class="card-title" id="dash-content-heading">Content</h2>
				<?php if ($_SESSION["userlevel"] > 2) { ?><a class="text-sm font-medium no-underline hover:underline" href="content.php">View all</a><?php } ?>
			</div>
			<div class="card-body">
<?php
if ($_SESSION["userlevel"] > 2) {
	// Join each page to its own service only (previously admins had no join condition,
	// so every page was counted once per service). Matches the count used by content.php.
	$count_query = "SELECT COUNT( * ) AS numpages, content.statusid, status FROM content LEFT JOIN status USING ( statusid ), cms.services WHERE content.serviceid=services.serviceid";
	if ($_SESSION["userlevel"] < 4) $count_query .= " AND (content.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]')";

	$count_query .= "  GROUP BY statusid";
	$contentrecs = $db->get_results($count_query);
	$status_badges = array('live' => 'badge-success', 'published' => 'badge-success', 'draft' => 'badge-neutral', 'expired' => 'badge-danger', 'pending' => 'badge-warning', 'awaiting approval' => 'badge-warning', 'archived' => 'badge-neutral');
	echo '<ul class="m-0 grid list-none grid-cols-2 gap-3 p-0 sm:grid-cols-3 lg:grid-cols-4">';
	if (is_array($contentrecs)) {
		foreach ($contentrecs as $status) {
			$badge = isset($status_badges[strtolower($status->status)]) ? $status_badges[strtolower($status->status)] : 'badge-primary';
			echo '<li class="m-0"><a class="group flex h-full flex-col gap-2 rounded-lg border border-line bg-surface p-4 no-underline transition-colors hover:border-line-strong hover:bg-surface-2" href="content.php?category=' . $status->statusid . '">';
			echo '<span class="badge ' . $badge . ' self-start">' . $status->status . '</span>';
			echo '<span class="text-2xl font-semibold tabular-nums tracking-tight text-fg">' . $status->numpages . '</span>';
			echo '<span class="text-xs text-fg-muted">page' . ($status->numpages == 1 ? '' : 's') . '</span>';
			echo '</a></li>';
		}
	}
	echo '</ul>';

	$attention = '';
	if ($reviews = $db->get_var("SELECT COUNT(*) AS numrevs FROM content WHERE (( TO_DAYS( reviewdate) - TO_DAYS( CURRENT_DATE ))  < 14 ) AND statusid != 4 AND reviewdate > CURRENT_DATE  AND content.serviceid = '$_SESSION[userserviceid]'")) {
		$attention .= '<a class="flex items-center gap-3 rounded-lg border border-warning-line bg-warning-soft px-4 py-3 text-warning no-underline transition-colors hover:brightness-[.98]" href="content.php?so=reviewdate&st=asc&category=">' . cms_icon('clock', 'icon-lg') . '<span class="flex-1 text-sm"><strong class="font-semibold">' . $reviews . ' Review pending</strong><span class="block text-xs opacity-90">Pages due for review within 14 days</span></span>' . cms_icon('chevron-right') . '</a>';
	}
	if ($reviews = $db->get_var("SELECT COUNT(*) AS numrevs FROM content WHERE reviewdate <= CURDATE() AND statusid != 4 AND content.serviceid = '$_SESSION[userserviceid]'")) {
		$attention .= '<a class="flex items-center gap-3 rounded-lg border border-danger-line bg-danger-soft px-4 py-3 text-danger no-underline transition-colors hover:brightness-[.98]" href="content.php?so=reviewdate&st=asc&category=">' . cms_icon('circle-alert', 'icon-lg') . '<span class="flex-1 text-sm"><strong class="font-semibold">' . $reviews . ' Review overdue</strong><span class="block text-xs opacity-90">Pages past their review date</span></span>' . cms_icon('chevron-right') . '</a>';
	}
	if ($attention) {
		echo '<h3 class="mb-3 mt-6 text-xs font-medium uppercase tracking-wider text-fg-subtle">Needs attention</h3>';
		echo '<div class="grid gap-3 sm:grid-cols-2">' . $attention . '</div>';
	}
} else {
	echo '<div class="empty-state py-6">' . cms_icon('inbox') . '<p class="m-0">None</p></div>';
}
?>
			</div>
		</section>

		<section class="card" aria-labelledby="dash-manage-heading">
			<div class="card-header">
				<h2 class="card-title" id="dash-manage-heading">Manage</h2>
				<span class="text-xs text-fg-subtle"><?php echo count($dash_areas); ?> area<?php echo count($dash_areas) == 1 ? '' : 's'; ?></span>
			</div>
			<div class="card-body">
<?php if (count($dash_areas) > 0) { ?>
				<ul class="m-0 grid list-none gap-2 p-0 sm:grid-cols-2 lg:grid-cols-3">
<?php
	foreach ($dash_areas as $area=>$title) {
		$inner = '<span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-primary-soft text-primary-text">' . cms_icon(cms_area_icon($area)) . '</span><span class="min-w-0 flex-1 text-sm font-medium leading-5 text-fg">' . $title . '</span>';
		$cls = 'flex h-full items-center gap-3 rounded-lg border border-transparent px-2.5 py-2 no-underline transition-colors hover:border-line hover:bg-surface-2';
		if (array_key_exists($area, $popups)) {
			$dim = explode(",", $popups[$area]);
			$width = $dim[0];
			$height = $dim[1];
			echo '<li class="m-0"><a class="' . $cls . '" href="#" onClick="window.open(\'' . $area . '.php\', \'school_sess\', \'width=' . $width . ',height=' . $height . ',top=50,left=50,resizable=yes,scrollbars=yes\'); return false;">' . $inner . cms_icon('external-link', 'icon-sm text-fg-subtle', 'opens in a new window') . '</a></li>';
		} else {
			echo '<li class="m-0"><a class="' . $cls . '" href="' . $area . '.php">' . $inner . '</a></li>';
		}
	}
?>
				</ul>
<?php } else { ?>
				<div class="empty-state py-6"><?php echo cms_icon('lock'); ?><p class="m-0">You do not currently have access to any CMS areas.</p></div>
<?php } ?>
			</div>
		</section>
	</div>

	<aside class="flex min-w-0 flex-col gap-6" aria-label="Tools">
		<section class="card" aria-labelledby="dash-tools-heading">
			<div class="card-header">
				<h2 class="card-title" id="dash-tools-heading">Tools</h2>
			</div>
			<ul class="m-0 list-none p-1.5">
<?php
$dash_tools = array(
	array('docs/style.pdf', 'ClacksWeb Style Guide', 'book-open', false),
	array('docs/content.pdf', 'Content task guide', 'file-text', false),
	array('docs/forms.pdf', 'Forms task guide', 'clipboard-list', false),
	array('docs/licensing.pdf', 'Licensing task guide', 'scale', false),
	array('docs/meetings.pdf', 'Meetings & agendas task guide', 'notebook-tabs', false),
	array('docs/writingfortheweb.pdf', 'Writing for the Web guidance notes', 'square-pen', false),
	array('http://www.xstandard.com/en/documentation/xstandard-end-user-guide/', 'XStandard user guide', 'circle-help', true),
);
foreach ($dash_tools as $tool) {
	echo '<li class="m-0"><a class="menu-item"' . ($tool[3] ? ' target="_blank"' : '') . ' href="' . $tool[0] . '">' . cms_icon($tool[2]) . '<span class="flex-1">' . htmlspecialchars($tool[1]) . '</span>';
	echo $tool[3] ? cms_icon('external-link', 'icon-sm', 'opens in a new window') : '<span class="badge badge-neutral">PDF</span>';
	echo '</a></li>';
}
?>
			</ul>
		</section>

		<section class="card" aria-labelledby="dash-account-heading">
			<div class="card-header">
				<h2 class="card-title" id="dash-account-heading">Your account</h2>
			</div>
			<div class="card-body flex items-center gap-3">
				<span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-soft text-sm font-semibold uppercase text-primary-text" aria-hidden="true"><?php echo htmlspecialchars(substr($_SESSION["username"], 0, 1)); ?></span>
				<div class="min-w-0 flex-1">
					<p class="m-0 truncate text-sm font-medium"><?php echo $_SESSION["username"]; ?></p>
					<p class="m-0 text-xs text-fg-muted">Signed in</p>
				</div>
				<a class="btn btn-sm btn-secondary" href="profile.php"><?php echo cms_icon('user-round'); ?>Profile</a>
			</div>
		</section>
	</aside>
</div>

<?php include "includes/footer.inc.php"; ?>
