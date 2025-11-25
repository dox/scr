<!DOCTYPE html>
<?php
include_once "inc/autoload.php";

if (!$user->isLoggedIn()) {
	header("Location: login.php");
}
?>
<html lang="en" data-bs-theme="auto">
<head>
	<?php require_once "layout/html_head.php"; ?>
</head>
<body>
	<?php require_once "layout/header.php"; ?>
	
	<div class="container">
		<?php
		// Determine which page to show
		$page = $_GET['page'] ?? 'index';
		$pageFile = "pages/{$page}.php";
		
		if (!file_exists($pageFile)) {
			$pageFile = 'pages/404.php';
		}
		
		require_once $pageFile;
		?>
	</div>
	
	<?php require_once "layout/footer.php"; ?>
</body>
</html>
