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

if (!$user->hasPermission("members") && $member->ldap !== $user->getUsername()) {
	die("User not permitted to see member stats.");
}

$terms = new Terms();
$meals = new Meals();
$today = new DateTimeImmutable('today');
$currentTerm = $terms->currentTerm();
$previousTerm = $terms->previousTerm();

// Determine date range based on scope
$scope = $_GET['scope'] ?? 'all';
switch ($scope) {
	case 'term_previous':
		$start = new DateTimeImmutable($previousTerm->date_start);
		$end   = new DateTimeImmutable($previousTerm->date_end);
		break;

	case 'term':
		$start = new DateTimeImmutable($currentTerm->date_start);
		$end   = $today;
		break;

	case 'ytd':
		$start = new DateTimeImmutable($today->format('Y-01-01'));
		$end   = $today;
		break;
		
	case '12m':
		$start = $today->modify('-1 year');
		$end   = $today;
		break;

	case 'all':
	default:
		$start = $meals->oldestMealDate();
		$end   = $today;
		break;
}

// Fetch bookings
$totalBookings = $member->countBookingsByTypeBetweenDates(
	$start->format('Y-m-d'),
	$end->format('Y-m-d')
);

// Helper: choose Bootstrap column class based on count
function getColClass(int $count): string {
	$map = [1 => 'col-12', 2 => 'col-6', 3 => 'col-4', 4 => 'col-6 col-sm-6 col-lg-3'];
	return $map[$count] ?? 'col-12';
}

// Output
if (empty($totalBookings)) {
	echo "No meals booked between " . formatDate($start, 'short') . " and " . formatDate($end, 'short');
} else {
	$totalBookings = array_slice($totalBookings, 0, 4, true);
	$colClass = getColClass(count($totalBookings));
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
}
