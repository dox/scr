<?php
// Load configuration before starting the session so cookie policy is applied
// consistently to every entry point.
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../inc/global.php';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
	'lifetime' => 0,
	'path'     => '/',
	'domain'   => '',
	'secure'   => true,
	'httponly' => true,
	'samesite' => 'Strict',
]);
session_start();

// Set debugging
if (APP_DEBUG) {
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);

	set_error_handler(function ($errno, $errstr, $errfile, $errline) {
		echo "<div class=\"alert alert-danger\" role=\"alert\">";
		echo "<strong>PHP ERROR:</strong> [$errno] $errstr<br>";
		echo "In <strong>$errfile</strong> on line <strong>$errline</strong>";
		echo "</div>";
		return false;
	});

	set_exception_handler(function ($e) {
		echo "<div class=\"alert alert-warning\" role=\"alert\">";
		echo "<strong>UNCAUGHT EXCEPTION:</strong> " . get_class($e) . "<br>";
		echo $e->getMessage() . "<br><br>" . $e->getTraceAsString();
		echo "</div>";
	});
} else {
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
	error_reporting(0);

	ini_set('log_errors', '1');
	ini_set('error_log', __DIR__ . '/php-error.log');
}

// Register autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load classes
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Model.php';
require_once __DIR__ . '/../classes/Term.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Member.php';
require_once __DIR__ . '/../classes/Meal.php';
require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Wine.php';
require_once __DIR__ . '/../classes/Cellar.php';
require_once __DIR__ . '/../classes/Bin.php';
require_once __DIR__ . '/../classes/Transaction.php';
require_once __DIR__ . '/../classes/WineList.php';

// Initialise shared database instance
try {
	global $db;
	$db = Database::getInstance();
} catch (Throwable $e) {
	error_log("Database connection failed: " . $e->getMessage());
	die('<h1>Database connection error: ' . htmlspecialchars($e->getMessage()) . '</h1>');
}

// Create shared objects
$log      = new Log();
$terms    = new Terms();
$meals    = new Meals();
$user     = new User();
$settings = new Settings();

// Handle impersonation only for an authenticated user authorized to do so.
// Validate both the identifier and the database record before changing the session.
if (!empty($_POST['impersonate'])) {
	$targetId = filter_input(INPUT_POST, 'impersonate', FILTER_VALIDATE_INT, [
		'options' => ['min_range' => 1]
	]);

	if (!$user->isLoggedIn() || !$user->hasPermission('impersonate')) {
		$log->add("SECURITY ALERT: Unauthorized impersonation attempt from {$_SERVER['REMOTE_ADDR']}", 'auth', Log::WARNING);
	} elseif ($targetId === false || $targetId === null) {
		$log->add("SECURITY ALERT: Invalid impersonation target from {$_SERVER['REMOTE_ADDR']}", 'auth', Log::WARNING);
	} else {
		$originalMember = Member::fromUID((string) $user->getUID());
		$member = Member::fromUID((string) $targetId);

		if (!$originalMember->uid || !$member->uid) {
			$log->add("SECURITY ALERT: Nonexistent impersonation target {$targetId} from {$_SERVER['REMOTE_ADDR']}", 'auth', Log::WARNING);
		} else {
			$maintainAdminAccess = !empty($_POST['maintainAdminAccess']);
			$log->add("{$originalMember->name()} impersonating {$member->ldap} ({$member->name()})", 'member', Log::INFO);

			$_SESSION['impersonation_backup'] = $_SESSION['user'];
			$existingPermissions = $_SESSION['user']['permissions'] ?? [];
			$_SESSION['impersonating'] = true;
			setUserSessionFromMember($member, $maintainAdminAccess ? $existingPermissions : null);
			// Treat impersonation as a privilege boundary as well.
			session_regenerate_id(true);
			$user = new User();
		}
	}
}

// Restore impersonation
if (!empty($_POST['restore_impersonation']) && !empty($_SESSION['impersonation_backup'])) {
	$impersonatedUsername = $_SESSION['user']['samaccountname'];

	// Restore original session
	$_SESSION['user'] = $_SESSION['impersonation_backup'];
	unset($_SESSION['impersonation_backup'], $_SESSION['impersonating']);
	session_regenerate_id(true);

	$user = new User();
	$log->add("{$_SESSION['user']['name']} no longer impersonating {$impersonatedUsername}", 'member', Log::INFO);
}
