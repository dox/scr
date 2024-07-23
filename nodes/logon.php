<style>
.divider:after,
.divider:before {
  content: "";
  flex: 1;
  height: 1px;
  background: #eee;
}
</style>

<div class="row d-flex justify-content-center align-items-center h-100">
	<div class="col-md-9 col-lg-6 col-xl-6 my-lg-5 py-lg-5">
		<img src="/img/logo.png" alt="Site Logo" class="img-fluid" width="30%" />
		<h1 class="pt-4"><?php echo site_name; ?></h1>
		<?php
		if (debug) {
			$output  = "<div class=\"alert alert-warning\" role=\"alert\"><strong>DEBUG MODE</strong> IS ENABLED</div>";
			echo $output;
		}
		?>
	</div>
	
	<div class="col-md-8 col-lg-6 col-xl-5 offset-xl-1 my-lg-5 py-lg-5">
		<form method="post" id="loginSubmit" action="index.php">
			<div class="divider d-flex align-items-center my-4">
				<p class="text-center fw-bold mx-3 mb-0">Sign In</p>
			</div>
			
			<div class="pt-1 mb-4 d-grid gap-2">
				<?php
				if (isset($_SESSION['logon_error']) && $_GET['logout'] != "true") {
					echo "<div class=\"alert alert-warning\" role=\"alert\">";
					echo $_SESSION['logon_error'];
					echo "</div>";
				}
				?>
			</div>
			
			<div class=" mb-4">
				<label class="form-label" for="username">Username</label>
				<input type="text" id="username" name="username" class="form-control form-control-lg" required />
			</div>
			
			<div class="mb-4">
				<label class="form-label" for="password">Password</label>
				<input type="password" id="password" name="password" class="form-control form-control-lg" required />
			</div>
			
			<div class="d-flex justify-content-between align-items-center">
				<div class="mb-0 form-check">
					<input class="form-check-input" type="checkbox" id="remember_me" name="remember_me" value="true" id="flexCheckDefault">
					<label class="form-check-label" for="flexCheckDefault">Remember Me</label>
				</div>
				
				<a href="<?php echo reset_url; ?>" class="text-body">Forgot password?</a>
			</div>
			
			<div class="text-center d-grid text-lg-start mt-4 pt-2">
				<button class="btn btn-primary btn-lg" type="submit">Login</button>
			</div>
		</form>
	</div>
</div>