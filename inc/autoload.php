<?php
// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../inc/global.php';

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

// Handle impersonation
if (!empty($_POST['impersonate'])) {
	$targetId = $_POST['impersonate'];
	$maintainAdminAccess = $_POST['maintainAdminAccess'] ?? 0;

	// Ensure targetId is valid
	if ($targetId && is_numeric($targetId)) {
		$originalMember = Member::fromUID($_SESSION['user']['uid']);
		$member = Member::fromUID($targetId);

		$log->add("{$originalMember->name()} impersonating {$member->ldap} ({$member->name()})", 'member', Log::INFO);

		// Backup original session
		$_SESSION['impersonation_backup'] = $_SESSION['user'];
		$existingPermissions = $_SESSION['user']['permissions'];

		// Set impersonated session
		$_SESSION['impersonating'] = true;
		setUserSessionFromMember($member, $maintainAdminAccess ? $existingPermissions : null);

		// Refresh User object
		$user = new User();
	}
}

// Restore impersonation
if (!empty($_POST['restore_impersonation']) && !empty($_SESSION['impersonation_backup'])) {
	$impersonatedUsername = $_SESSION['user']['samaccountname'];

	// Restore original session
	$_SESSION['user'] = $_SESSION['impersonation_backup'];
	unset($_SESSION['impersonation_backup'], $_SESSION['impersonating']);

	$user = new User();
	$log->add("{$_SESSION['user']['name']} no longer impersonating {$impersonatedUsername}", 'member', Log::INFO);
}