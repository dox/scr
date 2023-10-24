<?php
include_once("inc/autoload.php");

if (!empty($_POST['inputUsername']) && !empty($_POST['inputPassword']) && $_SESSION['logon'] != 1) {
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
if ($_SESSION['logon'] != true) {
	header("Location: " . siteURL() . "/logon.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include_once("views/html_head.php"); ?>
</head>

<body class="bg-body-tertiary">
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

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://help.seh.ox.ac.uk/assets/chat/chat.min.js"></script>
<script>
$(function() {
  new ZammadChat({
    title: 'Need IT Support?',
    background: '#6b7889',
    fontSize: '12px',
    chatId: 1
  });
});
</script>