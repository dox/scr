<?php
session_start();

$root = $_SERVER['DOCUMENT_ROOT'];

require_once($root . '/config.php');

if (debug) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(1);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

require $root . '/vendor/autoload.php';

use LdapRecord\Connection;

// Create a new connection:
$ldap_connection = new Connection([
	'hosts' => LDAP_SERVER,
	'port' => LDAP_PORT,
	'base_dn' => LDAP_BASE_DN,
	'username' => LDAP_BIND_DN,
		'password' => LDAP_BIND_PASSWORD,
		'use_tls' => LDAP_STARTTLS,
]);
try {
	$ldap_connection->connect();
} catch (\LdapRecord\Auth\BindException $e) {
	$error = $e->getDetailedError();

	echo $error->getErrorCode();
	echo $error->getErrorMessage();
	echo $error->getDiagnosticMessage();
}

require_once($root . '/inc/globalFunctions.php');
require_once($root . '/inc/database.php');
require_once($root . '/inc/class_settings.php');
require_once($root . '/inc/class_logs.php');
require_once($root . '/inc/class_notifications.php');
require_once($root . '/inc/class_term.php');
require_once($root . '/inc/class_terms.php');
require_once($root . '/inc/class_member.php');
require_once($root . '/inc/class_members.php');
require_once($root . '/inc/class_meal.php');
require_once($root . '/inc/class_meals.php');
require_once($root . '/inc/class_booking.php');
require_once($root . '/inc/class_bookings.php');
require_once($root . '/inc/class_wine.php');
require_once($root . '/inc/class_wineWine.php');
require_once($root . '/inc/class_wineCellars.php');
require_once($root . '/inc/class_wineTransactions.php');
require_once($root . '/inc/class_reports.php');
require_once($root . '/inc/PHPMailer/Exception.php');
require_once($root . '/inc/PHPMailer/PHPMailer.php');
require_once($root . '/inc/PHPMailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

$db = new db(db_host, db_username, db_password, db_name);
?>