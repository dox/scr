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
		   OR ip = INET_ATON(?)
		ORDER BY date DESC
		LIMIT 500
	";
	
	$like = '%' . $searchTerm . '%';
	
	$logResults = $db->fetchAll($sql, [$like, $like, $searchTerm]);
} else {
	$logResults = $log->getRecent($logsDisplay);
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

<div class="table-responsive">
<table class="table table-striped table-centered">
	<thead>
		<tr>
			<th style="width: 180px;">Date</th>
			<th>Type</th>
			<th>Username</th>
			<th>IP Address</th>
			<th>Event</th>
		</tr>
	</thead>
	<tbody id="logs-table-body">
		<?php
		$latestLogUID = 0;
		$chartData    = [];
		
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
			
			$event = $row['description'] ?? '';
			$output .= "<td class=\"text-wrap text-break\">" . $log->linkify($event) . "</td>";
			$output .= "</tr>";
			
			echo $output;

			if ($row['uid'] > $latestLogUID) {
				$latestLogUID = (int)$row['uid'];
			}

			$date = date('Y-m-d', strtotime($row['date']));
			$chartData[$date] = ($chartData[$date] ?? 0) + 1;
		}
		
		// fill in missing dates
		if (!empty($chartData)) {
			// find earliest and latest date keys
			$dates = array_keys($chartData);
			sort($dates); // ascending chronological order
			$startDate = new DateTime($dates[0]);
			$endDate   = new DateTime($dates[count($dates) - 1]);
		
			// ensure end date is inclusive
			$endDateInclusive = (clone $endDate)->modify('+1 day');
		
			$interval = new DateInterval('P1D');
			$period = new DatePeriod($startDate, $interval, $endDateInclusive);
		
			foreach ($period as $dt) {
				$d = $dt->format('Y-m-d');
				if (!isset($chartData[$d])) {
					$chartData[$d] = 0;
				}
			}
		}

		ksort($chartData);
		?>
	</tbody>
</table>
</div>

<script>
let latestLogUID = <?= (int)$latestLogUID ?>;
let logsSearchActive = <?= $searchTerm !== '' ? 'true' : 'false' ?>;

// Chart setup
const ctx = document.getElementById('chart_logsByDay');
const logChart = new Chart(ctx, {
	type: 'bar',
	data: {
		labels: <?= json_encode(array_keys($chartData)) ?>,
		datasets: [{
			label: 'Logs',
			data: <?= json_encode(array_values($chartData)) ?>,
			borderWidth: 2,
			tension: 0.3,
			pointRadius: 0,
			pointHoverRadius: 0
		}]
	},
	options: {
		maintainAspectRatio: false,
		plugins: { legend: { display: false } },
		scales: {
			x: { title: { display: false } },
			y: { beginAtZero: true, title: { display: false } }
		}
	}
});
</script>

<script>
if (!logsSearchActive && latestLogUID > 0) {
	setInterval(async () => {
		try {
			const res = await fetch(`/ajax/logs_fetch.php?after=${latestLogUID}`);
			const data = await res.json();

			if (data.logs && data.logs.length > 0) {
				const tbody = document.getElementById('logs-table-body');

				data.logs.forEach(row => {
					const tr = document.createElement('tr');
					tr.className = row.row_class;

					tr.innerHTML = `
						<td>${row.date}</td>
						<td>${row.type_badge}</td>
						<td>${row.username ?? ''}</td>
						<td>${row.ip}</td>
						<td class="text-wrap text-break">${row.event}</td>
					`;

					tbody.prepend(tr);
					latestLogUID = Math.max(latestLogUID, row.uid);

					// Update chart
					const rowDate = row.date.split(' ')[0]; // yyyy-mm-dd
					const idx = logChart.data.labels.indexOf(rowDate);
					if (idx !== -1) {
						logChart.data.datasets[0].data[idx] += 1;
					} else {
						// New day not in chart yet
						logChart.data.labels.push(rowDate);
						logChart.data.datasets[0].data.push(1);
					}
				});

				logChart.update();
			}
		} catch (e) {
			console.warn('Failed to fetch new logs', e);
		}
	}, 10000); // every 10 seconds
}
</script>
