<?php
	include_once("inc/autoload.php");

	if (isset($_GET['logout'])) {
		session_destroy();
	}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
  <?php include_once("views/html_head.php"); ?>

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

			if (debug) {
				$output  = "<div class=\"alert alert-warning\" role=\"alert\">";
				$output .= "<strong>Warning!</strong> This site is in <code>debug mode</code>.  It is for testing purposes only!";
				$output .= "</div>";

				echo $output;
			}

			if (isset($_SESSION['logon_error'])) {
				echo "<div class=\"alert alert-primary\" role=\"alert\">";
				echo "<p>" . $_SESSION['logon_error'] . "</p>";
				echo "<p><a href=\"" . reset_url . "\" class=\"alert-link\">Forgot your password?</a></p>";
				echo "</div>";
			}
			?>
	    <form method="post" id="loginSubmit" action="index.php">
	      <div class="mb-4 text-center">
					<svg width="76" height="64" <?php if(debug) { echo "class=\"text-warning\"";}?>>
						<use xlink:href="img/icons.svg#chough-regular"/>
					</svg>
	        <h1 class="h3 mb-3 font-weight-normal">SCR Meal Booking</h1>
	      </div>
	      <label for="inputUsername" class="visually-hidden">Username</label>
	      <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="Username" required autofocus>
	      <label for="inputPassword" class="visually-hidden">Password</label>
	      <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
	      <button class="btn btn-lg btn-primary w-100" type="submit">Log In</button>
				<?php
				if (!empty(reset_url)) {
					echo "<p class=\"mt-5 mb-3  text-center\">";
					echo "<a class=\"text-white\" href=\"" . reset_url . "\">Forgot your password?</a>";
					echo "</p>";
				}
				?>
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
