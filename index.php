<?php
include_once("inc/autoload.php");

// impersonate
if (isset($_POST['impersonate_ldap'])) {
  $logsClass->create("admin", $_SESSION['username'] . " impersonating " . $_POST['impersonate_ldap']);

  $_SESSION['username_original'] = $_SESSION['username'];
  $_SESSION['username'] = $_POST['impersonate_ldap'];
  $_SESSION['impersonating'] = "true";
}
// impersonate stop
if (isset($_POST['stop_impersonating'])) {
  $logsClass->create("admin", $_SESSION['username_original'] . " no longer impersonating " . $_SESSION['username']);

  $_SESSION['username'] = $_SESSION['username_original'];
  unset($_SESSION['username_original']);
  unset($_SESSION['impersonating']);
}

if (isset($_POST['inputUsername']) && isset($_POST['inputPassword'])) {
	if ($ldap_connection->auth()->attempt($_POST['inputUsername'] . LDAP_ACCOUNT_SUFFIX, $_POST['inputPassword'], $stayAuthenticated = true)) {
    $ldapUser = $ldap_connection->query()->where('samaccountname', '=', $_POST['inputUsername'])->get();
    // Successfully authenticated user.
		$_SESSION['logon'] = true;
		$_SESSION['username'] = strtoupper($_POST['inputUsername']);

    // Lookup user in SCR table
    $sql = "SELECT * FROM members where ldap = '" . escape($_SESSION['username']) . "';";
    $memberLookup = $db->query($sql)->fetchArray();

    if (!isset($memberLookup['uid'])) {
      // NEW user (MCR).  Create them!
      $NEWUSER['title'] = "??";
      $NEWUSER['enabled'] = "1";
      $NEWUSER['ldap'] = $ldapUser[0]['samaccountname'][0];
      $NEWUSER['firstname'] = $ldapUser[0]['givenname'][0];
      $NEWUSER['lastname'] = $ldapUser[0]['sn'][0];
      $NEWUSER['category'] = "MCR";
      $NEWUSER['type'] = "MCR";
      $NEWUSER['precedence'] = "0";
      $NEWUSER['email'] = $ldapUser[0]['mail'][0];

      $memberObject = new member();
      $memberObject->create($NEWUSER);
    } else {
      // UPDATE existing SCR member
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

      if (count($UPDATEUSER) > 1) {
        $memberObject = new member($memberLookup['uid']);
        $memberObject->update($UPDATEUSER);
      }
    }

    $arrayOfAdmins = explode(",", $settingsClass->value('member_admins'));
		if (in_array(strtoupper($_SESSION['username']), $arrayOfAdmins)) {
			$_SESSION['admin'] = true;
		} else {
			$_SESSION['admin'] = false;
		}

		$logsClass->create("logon_success", $_SESSION['username'] . " logon succesful");
	} else {
		// Username or password is incorrect.
		$_SESSION['logon'] = false;
		$_SESSION['username'] = null;
		$_SESSION['admin'] = false;
		$_SESSION['logon_error'] = "Incorrect username/password";

		$logsClass->create("logon_fail", $_POST['inputUsername'] . " logon failed");
	}
}
if ($_SESSION['logon'] != true) {
	header("Location: http://scr2.seh.ox.ac.uk/logon.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Andrew Breakspear">
  <title>St Edmund Hall: Meal Booking</title>

  <link rel="canonical" href="https://scr2.seh.ox.ac.uk/">

  <!-- Bootstrap core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/app.css" rel="stylesheet">

	<!-- Favicons -->
	<link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
  <link rel="manifest" href="/img/favicons/site.webmanifest">
	<link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
  <link rel="icon" href="/img/favicons/favicon.ico">
  <meta name="theme-color" content="#7952b3">

  <script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/app.js"></script>
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
