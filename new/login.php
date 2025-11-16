<!DOCTYPE html>
<?php
include_once "inc/autoload.php";

// Authenticate
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($user->authenticate($_POST['username'], $_POST['password'])) {
		header('Location: index.php');
	} else {
		$error = "<div class=\"alert alert-warning\" role=\"alert\">Username/password mismatch</div>";
	}
}
?>
<html lang="en" data-bs-theme="auto">
<head>
	<?php include_once "layout/html_head.php"; ?>
</head>
<body>
	<div class="container text-center">
		<div class="row justify-content-center pt-5">
			<div class="col-12 col-sm-8 col-md-6 col-lg-4 mx-auto">
				
				<form class="form-signin" method="post">
					<img src="assets/images/logo.png" alt="Site Logo" class="img-fluid mb-3" width="34%" />
					
					<div class="form-floating">
						<input type="text" class="form-control" id="username" name="username" required>
						<label for="username">Username</label>
					</div>
					<div class="form-floating">
						<input type="password" class="form-control" id="password" name="password" required>
						<label for="floatingPassword">Password</label>
					</div>
					<div class="form-floating text-end">
						<?php
						if (RESET_URL) {
							echo "<span class=\"form-label-description\">";
							echo "<a href=\"" . RESET_URL . "\" class=\"text-muted\">Forgot Password?</a>";
							echo "</span>";
						}
						?>
					</div>
					<button class="btn btn-primary w-100 py-2 my-3" type="submit">Sign in</button>
				</form>
			</div>
		</div>
	</div>
	<?php include_once "layout/footer.php"; ?>
</body>
</html>