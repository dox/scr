<?php
/**
 * Application bootstrap and autoloader
 * 
 * - Loads configuration
 * - Registers the class autoloader
 * - Creates a shared Database instance
 */

session_start();

# ------------------------------------------------------------
# 1. Load configuration
# ------------------------------------------------------------
$config = include(__DIR__ . '/../config/config.php');
$config = include(__DIR__ . '/../inc/global.php');

# ------------------------------------------------------------
# 2. Set debugging
# ------------------------------------------------------------
if (APP_DEBUG) {
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
	
	// optional: pretty formatting in browser
	set_error_handler(function ($errno, $errstr, $errfile, $errline) {
		echo "<div class=\"alert alert-danger\" role=\"alert\">";
		echo "<strong>PHP ERROR:</strong> [$errno] $errstr\n";
		echo "In <strong>$errfile</strong> on line <strong>$errline</strong>\n";
		echo "</div>";
		return false;
	});

	set_exception_handler(function ($e) {
		echo "<div class=\"alert alert-warning\" role=\"alert\">";
		echo "<strong>UNCAUGHT EXCEPTION:</strong> " . get_class($e) . "\n";
		echo $e->getMessage() . "\n\n" . $e->getTraceAsString();
		echo "</div>";
	});

} else {
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
	error_reporting(0);

	// log silently to file
	ini_set('log_errors', '1');
	ini_set('error_log', __DIR__ . '/php-error.log');
}

# ------------------------------------------------------------
# 3. Register class autoloader
# ------------------------------------------------------------
require_once __DIR__ . '/../classes/Database.php';


# ------------------------------------------------------------
# 4. Initialise shared Database instance
# ------------------------------------------------------------
try {
	global $db;
	$db = Database::getInstance();
} catch (Throwable $e) {
	// Handle connection errors gracefully
	error_log("Database connection failed: " . $e->getMessage());
	die('<h1>Database connection error: ' . $e->getMessage() . '</h1>');
}

require_once __DIR__ . '/../classes/Model.php';
require_once __DIR__ . '/../classes/Term.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Member.php';
require_once __DIR__ . '/../classes/Meal.php';
require_once __DIR__ . '/../classes/Booking.php';

$log = new Log();
$terms = new Terms();
$user = new User();
$settings = new Settings();


if (isset($_POST['impersonate_submit_button'])) {
	$targetId = $_POST['impersonate'] ?? null;

	if ($targetId) {
		// preserve the current session state
		$_SESSION['impersonation_backup'] = $_SESSION['user'];
		$existingPermissions = $_SESSION['user']['permissions'];

		// fetch the user being impersonated
		$member = Member::fromUID($targetId);

		// adopt their details
		$_SESSION['user']['uid']   = $member->uid;
		$_SESSION['user']['samaccountname']   = $member->ldap;
		$_SESSION['user']['permissions'] = $member->permissions();

		// optional: honour the checkbox
		if (isset($_POST['maintainAdminAccess'])) {
			$_SESSION['user']['permissions'] = $existingPermissions;
		}
	}
}

if (isset($_POST['restore_impersonation']) && isset($_SESSION['impersonation_backup'])) {
	// adopt their details
	$_SESSION['user']['uid']   = $_SESSION['impersonation_backup']['uid'];
	$_SESSION['user']['samaccountname']   = $_SESSION['impersonation_backup']['samaccountname'];
	$_SESSION['user']['permissions'] = $_SESSION['impersonation_backup']['permissions'];
	
	unset($_SESSION['impersonation_backup']);
}