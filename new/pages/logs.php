<?php
$user->pageCheck('logs');

$logs = $log->getRecent();

echo pageTitle(
	"Logs",
	"Admin/User logs for the last " . $settings->get('logs_display') . " days (" . $settings->get('logs_retention') . " days available)"
);
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Date</th>
			<th>Type</th>
			<th>Username</th>
			<th>IP Address</th>
			<th>Event</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($logs as $row) {
			if ($row['category'] == "INFO") {
				$typeBadgeClass = "text-bg-info";
			} elseif ($row['category'] == "WARNING") {
				$typeBadgeClass = "text-bg-warning";
			} elseif ($row['category'] == "ERROR") {
				$typeBadgeClass = "text-bg-danger";
			} elseif ($row['category'] == "DEBUG") {
				$typeBadgeClass = "text-bg-primary";
			} else {
				$typeBadgeClass = "text-bg-secondary";
			}
			
			$typeBadge = "<span class=\"badge rounded-pill " . $typeBadgeClass . "\">" . strtoupper($row['category']) . "</span>";
			$output  = "<tr>";
			$output .= "<td>" . $row['date'] . "</td>";
			$output .= "<td>" . $typeBadge . "</td>";
			$output .= "<td>" . $row['username'] . "</td>";
			$output .= "<td>" . long2ip($row['ip']) . "</td>";
			
			if (isset($row['description'])) {
				$event = nl2br(htmlspecialchars($row['description']));
			} else {
				$event = "";
			}
			$output .= "<td>" . $event . "</td>";
			$output .= "</tr>";
			
			echo $output;
			
		}
		?>
			
	</tbody>
</table>