<?php
$currentTerm = $terms->currentTerm();

echo pageTitle(
	"SCR Meal Booking",
	$currentTerm->name
);
?>

<ul class="nav nav-tabs mb-3 flex-nowrap" id="scrUserList" role="tablist">
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
		   data-selected="<?= $isCurrent ? 'true' : 'false' ?>"
		   data-url="./ajax/meals.php?date=<?= urlencode($week) ?>">
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
	<div class="tab-pane fade <?= $isCurrent ? 'show active' : '' ?> "
		id="<?= $paneId ?>"
		role="tabpanel"
		aria-labelledby="<?= $tabId ?>"
		data-url="./ajax/meals.php?week=<?= urlencode($week) ?>">
		
		<div class="d-flex justify-content-center">
		  <div class="spinner-border" role="status">
			<span class="visually-hidden">Loading...</span>
		  </div>
		</div>
	</div>
<?php endforeach; ?>
</div>

<script>
// Initialize week tabs
document.addEventListener('DOMContentLoaded', () => {
	initAjaxLoader('.nav-link', '#scrUserListContent');
});
</script>

<style>
.nav-tabs {
  overflow-x: auto;
  white-space: nowrap;
  -webkit-overflow-scrolling: touch; /* smooth scrolling on iOS */
}

.nav-tabs .nav-item {
  display: inline-block;
}

.nav-tabs .nav-link {
  white-space: nowrap; /* prevent breaking */
}
</style>


<div class="modal fade" id="menuModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-body" id="modalBody"></div>
	</div>
  </div>
</div>