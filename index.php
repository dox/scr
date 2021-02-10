<?php
include_once("inc/autoload.php");

if (isset($_POST['inputUsername']) && isset($_POST['inputPassword'])) {
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
      $NEWUSER['ldap'] = $ldapUser[0]['samaccountname'][0];
      $NEWUSER['firstname'] = $ldapUser[0]['givenname'][0];
      $NEWUSER['lastname'] = $ldapUser[0]['sn'][0];
      $NEWUSER['category'] = "MCR";
      $NEWUSER['type'] = "MCR";
      $NEWUSER['precedence'] = "0";
      $NEWUSER['email'] = $ldapUser[0]['mail'][0];
      $NEWUSER['enabled'] = "1";

      $memberObject->create($NEWUSER, false);
    } else {
			$memberObject = new member($memberLookup['uid']);

			$UPDATEUSER['date_lastlogon'] = date('Y-m-d H:i:s');
      // EXISTING user, fill our their missing details
      $UPDATEUSER['memberUID'] = $memberLookup['uid'];
      if (empty($memberLookup['firstname'])) {
        $UPDATEUSER['firstname'] = $ldapUser[0]['givenname'][0];
      }
      if (empty($memberLookup['lastname'])) {
        $UPDATEUSER['lastname'] = $ldapUser[0]['sn'][0];
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

    $arrayOfAdmins = explode(",", strtoupper($settingsClass->value('member_admins')));
		if (in_array(strtoupper($_SESSION['username']), $arrayOfAdmins)) {
			$_SESSION['admin'] = true;
		} else {
			$_SESSION['admin'] = false;
		}

		$logArray['category'] = "logon";
    $logArray['result'] = "success";
    $logArray['description'] = $_SESSION['username'] . " logon succesful";
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
if ($_SESSION['logon'] != true) {
	header("Location: " . siteURL() . "/logon.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="SCR Meal Booking system for St Edmund Hall">
  <meta name="author" content="Andrew Breakspear">
  <title>St Edmund Hall: Meal Booking</title>

  <link rel="canonical" href="<?php echo siteURL(); ?>">

  <!-- Bootstrap core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/app.css" rel="stylesheet">

	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/app.js"></script>

	<!-- Favicons -->
	<link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
  <link rel="manifest" href="/img/favicons/site.webmanifest">
	<link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
  <link rel="icon" href="/img/favicons/favicon.ico">
  <meta name="theme-color" content="#7952b3">
</head>

<body>
		<?php
		include_once("views/header.php");
		?>

    <div class="container">
      <?php
  		$node = "nodes/index.php";
  			if (isset($_GET['n'])) {
  				$node = "nodes/" . $_GET['n'] . ".php";

  				if (!file_exists($node)) {
  					$node = "nodes/404.php";
  				}
  			}
  		include_once($node);
      ?>
    </div>
    <?php
		include_once("views/footer.php");
		?>
</body>
</html>
