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
	  display: flex;
	  align-items: center;
	  padding-top: 40px;
	  padding-bottom: 40px;
	  background-color: #f5f5f5;
	  background: url('/img/cover.jpg') no-repeat center center fixed;
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
	}
	
	.form-signin {
	  max-width: 330px;
	  padding: 15px;
	}
	
	.form-signin .form-floating:focus-within {
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

<body class="text-center">
	<main class="form-signin w-100 m-auto">
		<?php
		if (debug) {
			$output  = "<div class=\"alert alert-warning\" role=\"alert\">";
			$output .= "<strong>Warning!</strong> This site is in <code>debug mode</code>.  It is for testing purposes only!";
			$output .= "</div>";
			//echo $output;
		}
		
		if (isset($_SESSION['logon_error'])) {
			echo "<div class=\"alert alert-warning\" role=\"alert\">";
			echo "<p>" . $_SESSION['logon_error'] . "</p>";
			echo "<p><a href=\"" . reset_url . "\" class=\"alert-link\">Forgot your password?</a></p>";
			//echo "</div>";
		}
		?>
		
		<svg width="72" height="72" class="mb-4 <?php if(debug) { echo "text-warning";} else { echo "text-white"; }?>">
			<use xlink:href="img/icons.svg#chough"/>
		</svg>
		
		<form method="post" id="loginSubmit" action="index.php">
			<h1 class="h3 mb-3 fw-normal text-white"><?php echo site_name;?></h1>
			
			<div class="form-floating">
			  <input type="text" class="form-control" id="inputUsername" name="inputUsername" placeholder="Username">
			  <label for="inputUsername">Username</label>
			</div>
			<div class="form-floating">
			  <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Password">
			  <label for="inputPassword">Password</label>
			</div>
			
			<!--<div class="checkbox mb-3">
			  <label class="text-white">
				<input type="checkbox" value="remember-me" id="inputRemember" name="inputRemember" value=true> Remember me
			  </label>
			</div>-->
			<button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
			
			<?php
			if (!empty(reset_url)) {
				echo "<p class=\"mt-5 mb-3  text-center\">";
				echo "<a class=\"text-white\" href=\"" . reset_url . "\">Forgot your password?</a>";
				echo "</p>";
			}
			?>
			
			<p class="mt-5 mb-3 text-white"><?php echo site_name;?> developed by <a href="https://github.com/dox" class="text-white">Andrew Breakspear</a></p>
	</main>
	
</body>
<?php
	$_SESSION['logon_error'] = null;
?>
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