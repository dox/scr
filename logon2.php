<?php
include_once("inc/autoload.php");

if (isset($_GET['logout'])) {
	session_destroy();
}
?>
<html lang="en" class="h-100">
<head>
	<?php include_once("views/html_head.php"); ?>
</head>
<body>
	<section class="vh-100">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-6 text-black">
					<div class="px-5 ms-xl-4">
						<img src="/img/logo.png" alt="Site Logo" width="100px" class="me-3 pt-5 mt-xl-4" />
						<h1 class="pt-4"><?php echo site_name; ?></h1>
					</div>
					
					<div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">
						<form style="width: 23rem;" method="post" id="loginSubmit" action="index.php">
							<div class="form-outline mb-4">
								<input type="text" id="inputUsername" name="inputUsername" class="form-control form-control-lg" />
								<label class="form-label" for="form2Example18">Username</label>
							</div>
							
							<div class="form-outline mb-4">
								<input type="password" id="inputPassword" name="inputPassword" class="form-control form-control-lg" />
								<label class="form-label" for="form2Example28">Password</label>
							</div>
							
							<div class="pt-1 mb-4">
								<button class="btn btn-info btn-lg btn-block" type="submit">Login</button>
							</div>
							
							<p class="mb-5 pb-lg-2" ><a class="text-muted" href="<?php echo reset_url; ?>">Forgot password?</a></p>
							<p class="text-muted small">Developed by <a href="https://github.com/dox">Andrew Breakspear</a></p>
						</form>
					</div>
				</div>
				
				<div class="col-sm-6 px-0 d-none d-sm-block">
					<img src="/img/cover.jpg" alt="Login image" class="w-100 vh-100" style="object-fit: cover; object-position: left;">
				</div>
			</div>
		</div>
	</section>
</body>
</html>
