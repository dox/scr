<?php
header('Content-Type: application/json');
require_once '../inc/autoload.php';
$user->pageCheck('wine');

$input = json_decode(file_get_contents('php://input'), true);

$wineUid = $input['wine_uid'] ?? null;
$listUid = $input['list_uid'] ?? null;

if (!$wineUid || !$listUid) {
	echo json_encode(['success' => false, 'message' => 'Missing wine_uid or list_uid']);
	exit;
}

$list = new WineList($listUid);
if (!$list->uid) {
	echo json_encode(['success' => false, 'message' => 'List not found']);
	exit;
}



// Toggle via the class method
$result = $list->toggle($wineUid);

echo json_encode($result);
