<?php
declare(strict_types=1);

require_once '../inc/autoload.php';

$user->pageCheck('logs');

$after = (int)($_GET['after'] ?? 0);
if ($after <= 0) {
	echo json_encode(['logs' => []]);
	exit;
}

$sql = "
	SELECT *
	FROM logs
	WHERE uid > ?
	ORDER BY uid ASC
	LIMIT 500
";

$rows = $db->fetchAll($sql, [$after]);

$logs = [];

foreach ($rows as $row) {

	/* Row class based on result */
	if ($row['result'] === 'INFO') {
		$rowClass = 'table-info';
	} elseif ($row['result'] === 'WARNING') {
		$rowClass = 'table-warning';
	} elseif ($row['result'] === 'ERROR') {
		$rowClass = 'table-danger';
	} elseif ($row['result'] === 'DEBUG') {
		$rowClass = 'table-primary';
	} elseif ($row['result'] === 'SUCCESS') {
		$rowClass = 'table-success';
	} else {
		$rowClass = '';
	}

	$typeBadge = '<span class="badge rounded-pill text-bg-info">'
			   . strtoupper($row['category'])
			   . '</span>';

	$event = '';
	if (!empty($row['description'])) {
		$event = $log->linkify($row['description']);
	}

	$logs[] = [
		'uid'         => (int)$row['uid'],
		'date'       => $row['date'],
		'username'   => $row['username'],
		'ip'         => long2ip((int)$row['ip']),
		'event'      => $event,
		'type_badge' => $typeBadge,
		'row_class'  => $rowClass
	];
}

header('Content-Type: application/json');
echo json_encode(['logs' => $logs]);
