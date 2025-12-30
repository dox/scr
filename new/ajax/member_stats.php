<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}

$memberUID = filter_input(INPUT_GET, 'memberUID', FILTER_SANITIZE_NUMBER_INT);
if (!$memberUID) {
	die("Invalid member UID.");
}

$member = Member::fromUID($memberUID);

if (!$user->hasPermission("members") && $member->ldap != $user->getUsername()) {
	die("User not permitted to see member stats.");
}

$terms = new Terms();
$currentTerm = $terms->currentTerm();
$previousTerm = $terms->previousTerm();

// Determine days for countBookingsByType
$scope = $_GET['scope'] ?? 'all';
switch ($scope) {
	case 'all':
		$start = "1970-01-01";
		$end = date('Y-m-d');
		break;
	case 'term_previous':
		$start = $previousTerm->date_start;
		$end = $previousTerm->date_end;
		break;
	case 'term':
		$start = $currentTerm->date_start;
		$end = date('Y-m-d');
		break;
	case 'ytd':
		$start = date('Y-m-d', strtotime('1 year ago'));
		$end = date('Y-m-d');
		break;
	default:
		$start = "1970-01-01";
		$end = date('Y-m-d');
		break;
}

$totalBookings = $member->countBookingsByTypeBetweenDates($start, $end);

if (empty($totalBookings)) {
	echo "No meals booked between " . formatDate($start, 'short') . " and " . formatDate($end, 'short');
} else {
	// Keep top 4 bookings, sorted
	$totalBookings = array_slice($totalBookings, 0, 4, true);

	// Determine Bootstrap column class
	$colClasses = [1 => 'col-12', 2 => 'col-6', 3 => 'col-4', 4 => 'col-6 col-sm-6 col-lg-3'];
	$colClass = $colClasses[count($totalBookings)] ?? 'col-12';
	?>
	
	<div class="row">
		<?php foreach ($totalBookings as $booking): ?>
			<div class="<?= $colClass ?>">
				<div class="card mb-3">
					<div class="card-body">
						<div class="subheader text-nowrap text-truncate"><?= htmlspecialchars($booking['type']) ?></div>
						<div class="h1 text-truncate"><?= htmlspecialchars($booking['total']) ?></div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

<?php
} // close else
