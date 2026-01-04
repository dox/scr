<?php
$user->pageCheck('logs');

$logsDisplay   = $settings->get('logs_display');
$logsRetention = $settings->get('logs_retention');

/* Accept the search term, if offered */
$searchTerm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['logs_search'])) {
	$searchTerm = trim($_POST['logs_search'] ?? '');

	$sql = "
		SELECT *
		FROM logs
		WHERE description LIKE ?
		   OR username    LIKE ?
		ORDER BY date DESC";

	$like = '%' . $searchTerm . '%';

	$logResults = $db->fetchAll($sql, [$like, $like]);
} else {
	$logResults = $log->getRecent(3);
}

echo pageTitle(
	'Logs',
	"Admin/User logs for the last {$logsDisplay} days ({$logsRetention} days available)"
);
?>

<div class="mb-3">
	<canvas id="chart_logsByDay" style="height: 250px;"></canvas>
</div>

<form method="post" id="logsSearchForm" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="input-group mb-3">
		<input
			type="text"
			class="form-control form-control-lg"
			name="logs_search"
			id="logs_search"
			placeholder="Search logs"
			value="<?= htmlspecialchars($searchTerm) ?>"
		>
		<button class="btn btn-outline-secondary" type="submit">
			Search
		</button>
	</div>
</form>

<table class="table table-striped">
	<thead>
		<tr>
			<th style="width: 180px;">Date</th>
			<th>Type</th>
			<th>Username</th>
			<th>IP Address</th>
			<th>Event</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$chartData = [];
		
		foreach ($logResults as $row) {
			if ($row['result'] == "INFO") {
				$typeBadgeClass = "table-info";
			} elseif ($row['result'] == "WARNING") {
				$typeBadgeClass = "table-warning";
			} elseif ($row['result'] == "ERROR") {
				$typeBadgeClass = "table-danger";
			} elseif ($row['result'] == "DEBUG") {
				$typeBadgeClass = "table-primary";
			} elseif ($row['result'] == "SUCCESS") {
				$typeBadgeClass = "table-success";
			} else {
				$typeBadgeClass = "";
			}
			
			$typeBadge = "<span class=\"badge rounded-pill text-bg-info\">" . strtoupper($row['category']) . "</span>";
			$output  = "<tr class=\"{$typeBadgeClass}\">";
			$output .= "<td>" . $row['date'] . "</td>";
			$output .= "<td>" . $typeBadge . "</td>";
			$output .= "<td>" . $row['username'] . "</td>";
			$output .= "<td>" . long2ip($row['ip']) . "</td>";
			
			if (isset($row['description'])) {
				$event = nl2br(htmlspecialchars($row['description']));
			} else {
				$event = "";
			}
			$output .= "<td class=\"text-wrap text-break\">" . $event . "</td>";
			$output .= "</tr>";
			
			echo $output;
			
		}
		?>
	</tbody>
</table>

<?php
foreach ($log->getRecent($logsDisplay) as $row) {
	$date = date('Y-m-d', strtotime($row['date']));
	$chartData[$date] = ($chartData[$date] ?? 0) + 1;
}

ksort($chartData);
?>
<script>
const ctx = document.getElementById('chart_logsByDay');
new Chart(ctx, {
	type: 'bar',
	data: {
		labels: <?= json_encode(array_keys($chartData)) ?>,
		datasets: [{
			label: 'Logs',
			data: <?= json_encode($chartData) ?>,
			borderWidth: 2,
			tension: 0.3,
			pointRadius: 0,
			pointHoverRadius: 0
		}]
	},
	options: {
		maintainAspectRatio: false,
		plugins: {
		  legend: { display: false }
		},
		scales: {
			x: { title: { display: false } },
			y: { beginAtZero: true, title: { display: false } }
		}
	}
});
</script>