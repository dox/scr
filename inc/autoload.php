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
require_once($root . '/inc/class_reports.php');
require_once($root . '/inc/PHPMailer/Exception.php');
require_once($root . '/inc/PHPMailer/PHPMailer.php');
require_once($root . '/inc/PHPMailer/SMTP.php');


use LdapRecord\Connection;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

$db = new db(db_host, db_username, db_password, db_name);

if (!empty($_POST['inputUsername']) && !empty($_POST['inputPassword']) && $_SESSION['logon'] != 1) {
   // Create a new connection:
   $ldap_connection = new Connection([
	   'hosts' => [LDAP_SERVER],
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
   
   // first LDAP auth this user...
   $ldapLookupUsername = escape($_POST['inputUsername']) . LDAP_ACCOUNT_SUFFIX;
   $ldapLookupPassword = $_POST['inputPassword'];
   if ($ldap_connection->auth()->attempt($ldapLookupUsername, $ldapLookupPassword, $stayAuthenticated = true)) {
	   // LDAP authentication correct, get the LDAP user
   $ldapUser = $ldap_connection->query()->where('samaccountname', '=', $_POST['inputUsername'])->get();
   
	   // Attempt to match the user in the SCR table
   $sql = "SELECT * FROM members where ldap = '" . $ldapUser[0]['samaccountname'][0] . "';";
   $memberLookup = $db->query($sql)->fetchArray();
   
   if (!isset($memberLookup['uid'])) {
		   $memberObject = new member();
   
	 // NEW user.  Create them and assume they are MCR...
	 $NEWUSER['title'] = "";
	 $NEWUSER['enabled'] = "1";
	 $NEWUSER['ldap'] = strtolower($ldapUser[0]['samaccountname'][0]);
	 $NEWUSER['firstname'] = addslashes($ldapUser[0]['givenname'][0]);
	 $NEWUSER['lastname'] = addslashes($ldapUser[0]['sn'][0]);
	 $NEWUSER['category'] = "Student";
	 $NEWUSER['type'] = "MCR";
	 $NEWUSER['email'] = $ldapUser[0]['mail'][0];
	 $NEWUSER['enabled'] = "1";
	 $NEWUSER['date_lastlogon'] = date('c');
	 $NEWUSER['calendar_hash'] = crypt($NEWUSER['ldap'], salt);
   
	 $memberObject->create($NEWUSER, false);
	 
	 $sql = "SELECT * FROM members where ldap = '" . $ldapUser[0]['samaccountname'][0] . "';";
	 $memberLookup = $db->query($sql)->fetchArray();
	 $memberObject = new member($memberLookup['uid']);
   } else {
		   $memberObject = new member($memberLookup['uid']);
   
		   $UPDATEUSER['date_lastlogon'] = date('Y-m-d H:i:s');
	 // EXISTING user, fill our their missing details
	 $UPDATEUSER['memberUID'] = $memberLookup['uid'];
	 if (empty($memberLookup['firstname'])) {
	   $UPDATEUSER['firstname'] = addslashes($ldapUser[0]['givenname'][0]);
	 }
	 if (empty($memberLookup['lastname'])) {
	   $UPDATEUSER['lastname'] = addslashes($ldapUser[0]['sn'][0]);
	 }
	 if (empty($memberLookup['email'])) {
	   $UPDATEUSER['email'] = $ldapUser[0]['mail'][0];
	 }
   
		   $memberObject->update($UPDATEUSER, false);
   }
   
	   // build the $_SESSION array
	   $_SESSION['logon'] = true;
	   $_SESSION['enabled'] = $memberObject->enabled;
	   $_SESSION['username'] = strtoupper($ldapUser[0]['samaccountname'][0]);
   $_SESSION['type'] = $memberObject->type;
   $_SESSION['category'] = $memberObject->category;
   
   $arrayOfAdmins = explode(",", strtoupper($settingsClass->value('member_admins')));
	   if (in_array(strtoupper($_SESSION['username']), $arrayOfAdmins)) {
		   $_SESSION['admin'] = true;
	   } else {
		   $_SESSION['admin'] = false;
	   }
   
   // build this out one day when I have time :-s
   if(!empty($_POST["inputRemember"])) {
	 //setcookie ("username",$_SESSION['username'],time()+ 3600);
	 //setcookie ("password",$_POST['inputPassword'],time()+ 3600);      
	 //echo "Cookies Set Successfuly";
   } else {
	 //setcookie("username","");
	 //setcookie("password","");
	 //echo "Cookies Not Set";
   }
   
	   $logArray['category'] = "logon";
   $logArray['result'] = "success";
   $logArray['description'] = "[memberUID:" . $memberObject->uid . "] (" . $memberObject->displayName() . ") logon succesful";
   $logsClass->create($logArray);
   } else {
	   // Username or password is incorrect.
	   //session_destroy();
	   $_SESSION['logon_error'] = "Incorrect username/password";
   
	   $logArray['category'] = "logon";
   $logArray['result'] = "warning";
   $logArray['description'] = $_POST['inputUsername'] . " logon failed";
   $logsClass->create($logArray);
   }
}

if ($_SESSION['logon'] != true && $_SERVER['REQUEST_URI'] != "/logon.php") {
	//header("Location: " . siteURL() . "/logon.php");
	exit;
}
?>
