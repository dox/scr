<?php
declare(strict_types=1);

require_once '../inc/autoload.php';

$user->pageCheck('logs');

$after = (int)($_GET['after'] ?? 0);

if ($after <= 0) {
	echo json_encode(['count' => 0]);
	exit;
}

$sql = "
	SELECT COUNT(*) AS cnt
	FROM logs
	WHERE uid > ?
";

$result = $db->fetchAll($sql, [$after]);

header('Content-Type: application/json');
echo json_encode([
	'count' => (int)($result[0]['cnt'] ?? 0)
]);
