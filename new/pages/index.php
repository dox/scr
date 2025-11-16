<?php
$currentTerm = $terms->currentTerm();

echo pageTitle(
	"SCR Meal Booking",
	$currentTerm->name
);
?>

<ul class="nav nav-tabs flex-nowrap" id="scrUserList" role="tablist">
<?php foreach ($terms->navbarWeeks() as $week): ?>
	<?php
		$weekName = $currentTerm->tabLabel(htmlspecialchars($week));
		$isCurrent = $terms->isCurrentWeek($week);
		$label = $isCurrent ? "<strong>$weekName</strong>" : $weekName;

		// Prefixed IDs
		$paneId = 'week-' . htmlspecialchars($week);
		$tabId  = 'week-tab-' . htmlspecialchars($week);
	?>
	<li class="nav-item" role="presentation">
		<a class="nav-link <?= $isCurrent ? 'active' : '' ?>"
		   id="<?= $tabId ?>"
		   data-bs-toggle="tab"
		   href="#<?= $paneId ?>"
		   role="tab"
		   aria-controls="<?= $paneId ?>"
		   aria-selected="<?= $isCurrent ? 'true' : 'false' ?>"
		   data-url="./ajax/meals.php?week=<?= urlencode($week) ?>">
		   <?= $label ?>
		</a>
	</li>
<?php endforeach; ?>
</ul>

<div class="tab-content" id="scrUserListContent">
<?php foreach ($terms->navbarWeeks() as $week): ?>
	<?php
		$paneId = 'week-' . htmlspecialchars($week);
		$tabId  = 'week-tab-' . htmlspecialchars($week);
		$isCurrent = $terms->isCurrentWeek($week);
	?>
	<div class="tab-pane fade <?= $isCurrent ? 'show active' : '' ?>"
		 id="<?= $paneId ?>"
		 role="tabpanel"
		 aria-labelledby="<?= $tabId ?>"
		 data-url="./ajax/meals.php?week=<?= urlencode($week) ?>">
		 <div class="text-muted">Loading...</div>
	</div>
<?php endforeach; ?>
</div>

<script>
// Initialize week tabs
initAjaxLoader('#scrUserList a[data-bs-toggle="tab"]', null, {event: 'shown.bs.tab', cache: true});

</script>


<style>
.nav-tabs {
  overflow-x: auto;
  overflow-y: hidden;
  display: -webkit-box;
  display: -moz-box;
}
.nav-tabs>li {
  float: none;
}
</style>