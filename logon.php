<?php
	include_once("inc/autoload.php");

	if (isset($_GET['logout'])) {
		session_destroy();
	}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="SCR Meal Booking system for St Edmund Hall">
  <meta name="author" content="Andrew Breakspear">
  <title>SCR Meal Booking</title>

  <link rel="canonical" href="https://v5.getbootstrap.com/docs/5.0/examples/cover/">

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

	<style>
	body {
	  text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);
	  box-shadow: inset 0 0 5rem rgba(0, 0, 0, .9);
	  background: url('/img/cover.jpg') no-repeat center center fixed;
	  -webkit-background-size: cover;
	  -moz-background-size: cover;
	  -o-background-size: cover;
	  background-size: cover;
	}

	.form-signin {
	  width: 100%;
	  max-width: 330px;
	  padding: 15px;
	  margin: auto;
	}
	.form-signin .checkbox {
	  font-weight: 400;
	}
	.form-signin .form-control {
	  position: relative;
	  box-sizing: border-box;
	  height: auto;
	  padding: 10px;
	  font-size: 16px;
	}
	.form-signin .form-control:focus {
	  z-index: 2;
	}
	.form-signin input[type="text"] {
	  margin-bottom: -1px;
	  border-bottom-right-radius: 0;
	  border-bottom-left-radius: 0;
	}
	.form-signin input[type="password"] {
	  margin-bottom: 10px;
	  border-top-left-radius: 0;
	  border-top-right-radius: 0;
	}
	</style>
</head>

<body class="d-flex h-100 text-center text-white bg-dark">
	<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="mb-auto">
		</header>

		<main class="form-signin">
			<?php
			if (isset($_SESSION['logon_error'])) {
				echo "<div class=\"alert alert-primary\" role=\"alert\">";
				echo $_SESSION['logon_error'] . " <a href=\"" . reset_url . "\" class=\"alert-link\">Forgot your password?</a>";
				echo "</div>";
			}
			?>
	    <form method="post" id="loginSubmit" action="index.php">
	      <div class="mb-4 text-center">
					<svg width="76" height="64">
						<use xlink:href="img/icons.svg#chough-regular"/>
					</svg>
	        <h1 class="h3 mb-3 font-weight-normal">SCR Meal Booking</h1>
	      </div>
	      <label for="inputUsername" class="visually-hidden">Username</label>
	      <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="Username" required autofocus>
	      <label for="inputPassword" class="visually-hidden">Password</label>
	      <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
	      <button class="btn btn-lg btn-primary w-100" type="submit">Log In</button>
	      <p class="mt-5 mb-3  text-center"><a class="text-white" href="<?php echo reset_url; ?>">Forgot your password?</a></p>
	    </form>
	  </main>
		<footer class="mt-auto text-white-50">
			<p><a href="https://github.com/dox/scr" class="text-white">SCR Meal Booking</a> developed by <a href="https://github.com/dox" class="text-white">Andrew Breakspear</a>.</p>
		</footer>
    <?php
			$_SESSION['logon_error'] = null;
		?>
	</div>
</body>
</html>
