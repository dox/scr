<?php
include_once("inc/autoload.php");

if (isset($_GET['logout'])) {
	session_destroy();
}
?>
<html lang="en" class="h-100">
<head>
	<?php include_once("views/html_head.php"); ?>
	<style>
		body {
		  background: url('/img/cover.jpg') no-repeat center center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}
	</style>
</head>
<body>
	<!-- Section: Design Block -->
	<section class="text-center">
	  <!-- Background image -->
	  
	  <!-- Background image -->
	
	  <div class="card mt-5 mx-3 mx-md-5 shadow-5-strong" style="
			margin-top: -100px;
			background: hsla(0, 0%, 100%, 0.5);
			backdrop-filter: blur(30px);
			">
		<div class="card-body py-5 px-md-5">
	
		  <div class="row d-flex justify-content-center">
			<div class="col-lg-6">
				<img src="/img/logo.png" alt="Site Logo" width="100px" class="me-3 pt-5 mt-xl-4" />
			  <h2 class="fw-bold mb-5"><?php echo site_name; ?></h2>
			  
			  <form method="post" id="loginSubmit" action="index.php">
				  <div class=" mb-4">
					  <label class="form-label" for="inputUsername">Username</label>
					  <input type="text" id="inputUsername" name="inputUsername" class="form-control form-control-lg" />
				  </div>
				  
				  <div class="mb-4">
					  <label class="form-label" for="inputPassword">Password</label>
					  <input type="password" id="inputPassword" name="inputPassword" class="form-control form-control-lg" />
				  </div>
				  
				  <div class="pt-1 mb-4 d-grid gap-2">
					  <button class="btn btn-primary btn-lg" type="submit">Login</button>
					  <p class="mb-4 pb-lg-2"><a class="text-muted" href="<?php echo reset_url; ?>">Forgot password?</a></p>
				  </div>
				  
				  <div class="pt-1 mb-4 d-grid gap-2">
					  <p class="text-muted small">Developed by <a href="https://github.com/dox">Andrew Breakspear</a>
					  <?php
					  if (debug) {
						  $output  = "<span class=\"badge text-bg-warning\">DEBUG MODE</span>";
						  echo $output;
					  }
					  ?>
					  </p>
				  </div>
				  
			  </form>
			</div>
		  </div>
		</div>
	  </div>
	</section>
	<!-- Section: Design Block -->
	
	
</body>
</html>