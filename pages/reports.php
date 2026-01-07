<?php
require_once "inc/reports.php";

$user->pageCheck('reports');

echo pageTitle(
	"Reports",
	"Export data for members, meals, wine, etc."
);
?>

<div class="list-group">
	<?php
	foreach ($reports as $slug => $report) {
		echo renderReportItem(
			$report['title'],
			$report['description'],
			$report['format'],
			'report.php?page=' . $slug,
			$report['requiresDateRange'],
			$report['hidden'] ?? false
		);
	}
	?>
</div>

<?php
function renderReportItem(
	string $title,
	string $description,
	string $format,
	string $url,
	bool $requiresDateRange = false,
	bool $hidden = false
): string {
	if ($hidden) {
		return false;
	}

	$terms = new Terms();
	$previousTerm = $terms->previousTerm();
	$start = $previousTerm->date_start;
	$end   = $previousTerm->date_end;

	$format = strtolower($format);

	if (!in_array($format, ['html', 'csv'], true)) {
		throw new InvalidArgumentException('Invalid report format');
	}

	$title       = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
	$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
	$url         = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

	$badgeClass = $format === 'html' ? 'bg-danger' : 'bg-success';
	$badgeLabel = strtoupper($format);

	$output  = '<div class="list-group-item">';
	$output .= $requiresDateRange ? '<form method="post" action="' . $url . '">' : '';

	$output .= '
	<div class="row gy-3 align-items-start align-items-md-center">

		<!-- Title / description -->
		<div class="col-12 col-md-5">
			<h5 class="mb-1">' . $title . '</h5>
			<small class="text-muted">' . $description . '</small>
		</div>

		<!-- Date range -->
		<div class="col-12 col-md-3">';

	if ($requiresDateRange) {
		$output .= '
			<div class="row g-2">
				<div class="col-12 col-sm-6">
					<input
						type="date"
						class="form-control form-control-sm"
						name="from_date"
						value="' . htmlspecialchars($start) . '"
						required
						aria-label="From date"
					>
				</div>
				<div class="col-12 col-sm-6">
					<input
						type="date"
						class="form-control form-control-sm"
						name="to_date"
						value="' . htmlspecialchars($end) . '"
						required
						aria-label="To date"
					>
				</div>
			</div>';
	}

	$output .= '
		</div>

		<!-- Badge -->
		<div class="col-6 col-md-2 d-flex align-items-center">
			<span class="badge ' . $badgeClass . '">' . $badgeLabel . '</span>
		</div>

		<!-- Action -->
		<div class="col-6 col-md-2 text-end">';

	if ($requiresDateRange) {
		$output .= '
			<button type="submit" class="btn btn-sm btn-primary w-100 w-md-auto">
				Run
			</button>';
	} else {
		$output .= '
			<a href="' . $url . '" class="btn btn-sm btn-primary w-100 w-md-auto">
				Run
			</a>';
	}

	$output .= '
		</div>

	</div>';

	$output .= $requiresDateRange ? '</form>' : '';
	$output .= '</div>';

	return $output;
}
?>
