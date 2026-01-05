<!DOCTYPE html>
<?php
require_once "inc/autoload.php";

if (!$user->isLoggedIn()) {
	header("Location: login.php");
}
?>
<html lang="en" data-bs-theme="auto">
<head>
	<?php require_once "layout/html_head.php"; ?>
</head>
<body>
	<?php require_once "layout/header_debug.php"; ?>
	<?php require_once "layout/header.php"; ?>
	
	<div class="container">
		<?php
		// Determine which page to show
		$allowed = ['404','booking','guestlist','impersonate','index','information','logs','meal','meals','member','members','settings','term','terms','test','wine_index','wine_cellar','wine_bin','wine_wine','wine_filter','wine_wine_edit','wine_transactions','wine_transaction','wine_transaction_add'];
		
		$page = $_GET['page'] ?? 'index';
		
		if (!in_array($page, $allowed, true)) {
			$page = '404';
		}
		
		require_once __DIR__ . "/pages/{$page}.php";
		?>
	</div>
	
	<?php require_once "layout/footer.php"; ?>
</body>
</html>
