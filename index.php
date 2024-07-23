<?php
include_once("inc/autoload.php");

$_SESSION['logon_error'] = null;

$logArray['category'] = "logon";

$node = "nodes/logon.php";

if (isset($_GET['logout'])) {
  $logArray['result'] = "success";
  $logArray['description'] = $_SESSION['username'] . " logout";
  $logsClass->create($logArray);
  
  session_destroy();
  $_SESSION = null;
  unset($_COOKIE['username']);
  unset($_COOKIE['password']);
  unset($_COOKIE['token']);
  
  setcookie ("username", "", -1); 
  setcookie ("password", "", -1);
  setcookie ("token", "", -1);
}





if (isLoggedIn()) {
  // already logged in
  if (isset($_GET['n'])) {
    $node = "nodes/" . $_GET['n'] . ".php";
  } else {
    $node = "nodes/index.php";
  }
  
} elseif (!empty($_POST['username']) && !empty($_POST['password'])) {
    // attempt login with submitted credentials
    if (attemptLogin($_POST['username'], $_POST['password'], $_POST['remember_me'])) {
      // logged in with submitted credentials
      
      $logArray['result'] = "success";
      $logArray['description'] = $_POST['username'] . " logon success";
      if (isset($_POST['remember_me'])) {
        $logArray['description'] .= " (remember me: " . $_POST['remember_me'] . ")";
      }
      $logsClass->create($logArray);
      $node = "nodes/index.php";
    } else {
      // failed login with submitted credentials
    
      $_SESSION['logon_error'] = "Username/password incorrect";
      
      $logArray['result'] = "warning";
      $logArray['description'] = $_POST['username'] . " logon failed";
      $logsClass->create($logArray);
    }
} else {
  // not currently logged in, try with cookies
  if (isset($_COOKIE["username_uid"]) && isset($_COOKIE["token"])) {
    if (attemptLoginByCookie()) {
      // logged in via remember me credentials
      
      $logArray['result'] = "success";
      $logArray['description'] = $_SESSION['username'] . " logon success with cookies";
      $logsClass->create($logArray);
      
      $node = "nodes/index.php";
    } else {
      // cookie log-in failed
      
      $logArray['result'] = "warning";
      $logArray['description'] = $_COOKIE['username_uid'] . " logon failed with cookies";
      $logsClass->create($logArray);
    }
  }
}

function attemptLogin($username, $password, $remember_me = false) {
  global $ldap_connection, $db, $logsClass;
  
  // first LDAP auth this user...
  $clean_username = escape($username);
  $clean_password = $password;
  
  if ($ldap_connection->auth()->attempt($clean_username . LDAP_ACCOUNT_SUFFIX, $clean_password, $stayAuthenticated = true)) {
    // LDAP authentication correct, get the LDAP user
    $ldapUser = $ldap_connection->query()->where('samaccountname', '=', $clean_username)->get();
    
    // Attempt to match the user in the SCR table
    $sql = "SELECT * FROM members where ldap = '" . $ldapUser[0]['samaccountname'][0] . "' LIMIT 1";
    $memberLookup = $db->query($sql)->fetchArray();
    
    if (isset($memberLookup['uid'])) {
      $memberObject = new member($memberLookup['uid']);
      
      $_SESSION['logon'] = true;
      $_SESSION['enabled'] = $memberObject->enabled;
      $_SESSION['username'] = strtoupper($memberObject->ldap);
      $_SESSION['type'] = $memberObject->type;
      $_SESSION['category'] = $memberObject->category;
      $_SESSION['permissions'] = explode(",", $memberObject->permissions);
      
      if($remember_me == true) {
        $token = bin2hex(random_bytes(16));
        $token_expiry =  date('c', strtotime("1 month"));
        
        $sql = "REPLACE INTO tokens (token, member_uid, token_expiry) VALUES ('" . $token . "', '" . $memberObject->uid . "', '" . $token_expiry . "')";
        
        $tokenCreate = $db->query($sql);
        
        setcookie ("username_uid", $memberObject->uid, time()+ 3600);
        setcookie ("token", $token, time()+ 3600);      
      }
      
      return true;
    } else {
      $logArray['result'] = "warning";
      $logArray['description'] = $clean_username . " authenticated, but did not have access";
      $logsClass->create($logArray);
      
      $_SESSION['logon_error'] = "You have not been granted access to the SCR Booking System yet.  Please contact <a href=\"mailto:principals.ea@seh.ox.ac.uk\">principals.ea@seh.ox.ac.uk</a> if you believe this is in error";
      return false;
    }
  } else {
    return false;
  }
}

function attemptLoginByCookie() {
  global $db;
  
  $clean_username_uid = escape($_COOKIE['username_uid']);
  $clean_token = $_COOKIE['token'];
  
  $sql = "SELECT * FROM tokens WHERE member_uid = '" . $clean_username_uid . "' AND token = '" . $clean_token . "' ORDER BY token_expiry DESC LIMIT 1";
  $token_session = $db->query($sql)->fetchArray();
  
  if (strtotime($token_session['token_expiry']) > strtotime('now')) {
    $memberObject = new member($clean_username_uid);
    
    $_SESSION['logon'] = true;
    $_SESSION['enabled'] = $memberObject->enabled;
    $_SESSION['username'] = strtoupper($memberObject->ldap);
    $_SESSION['type'] = $memberObject->type;
    $_SESSION['category'] = $memberObject->category;
    $_SESSION['permissions'] = explode(",", $memberObject->permissions);
    
    return true;
  } else {
    // token expired
    return false;
  }
}

/*if (!empty($_POST['inputUsername']) && !empty($_POST['inputPassword']) && $_SESSION['logon'] != 1) {
  

    // build the $_SESSION array
    $_SESSION['logon'] = true;
    $_SESSION['enabled'] = $memberObject->enabled;
    $_SESSION['username'] = strtoupper($ldapUser[0]['samaccountname'][0]);
    $_SESSION['type'] = $memberObject->type;
    $_SESSION['category'] = $memberObject->category;
    $_SESSION['permissions'] = explode(",", $memberObject->permissions);
    
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
*/

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include_once("views/html_head.php"); ?>
</head>

<body class="bg-body-tertiary">
    <?php
    if (isLoggedIn()) {
      include_once("views/header.php");
    }
    ?>

    <div class="container">
      <?php
      if (!file_exists($node)) {
        $node = "nodes/404.php";
      }
      include_once($node);
      ?>
    </div>
    <?php
    include_once("views/footer.php");
    ?>
</body>
</html>

<script src="https://help.seh.ox.ac.uk/assets/chat/chat-no-jquery.min.js"></script>
<script>
(function() {
  new ZammadChat({
    title: 'Need IT Support?',
    background: '#6b7889',
    fontSize: '12px',
    chatId: 1
  });
})();
</script>