<section class="vh-100">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-6">
				<div class="px-5 ms-xl-4">
					<img src="/img/logo.png" alt="Site Logo" width="100px" class="me-3 pt-5 mt-xl-4" />
					<h1 class="pt-4"><?php echo site_name; ?></h1>
					
				</div>
				
				<div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">
					
					<form style="width: 23rem;" method="post" id="loginSubmit" action="index.php">
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
							<input type="text" id="username" name="username" class="form-control" required />
						</div>
						
						<div class="mb-4">
							<label class="form-label" for="password">Password</label>
							<input type="password" id="password" name="password" class="form-control" required />
						</div>
						
						<div class="mb-4 form-check">
						  <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me" value="true" id="flexCheckDefault">
						  <label class="form-check-label" for="flexCheckDefault">Remember Me</label>
						</div>
						
						<div class="pt-1 mb-4 d-grid gap-2">
							<button class="btn btn-primary" type="submit">Login</button>
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
			
			<div class="col-sm-6 px-0 d-none d-sm-block">
				<img src="/img/cover.jpg" alt="Login image" class="w-100 vh-100" style="object-fit: cover; object-position: center;">
			</div>
		</div>
	</div>
</section>