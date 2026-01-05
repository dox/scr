<?php
header('Content-Type: application/json');

require_once '../inc/autoload.php';

try {
	// Only POST
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
		exit;
	}

	// Basic sanity checks
	$wineUIDs = array_filter($_POST['wine_uid'] ?? [], fn($uid) => (int)$uid > 0);
	if (empty($wineUIDs)) {
		echo json_encode(['success' => false, 'message' => 'No wines selected.']);
		exit;
	}

	// Generate linked ID if more than one wine
	$linkedID = count($wineUIDs) > 1 ? bin2hex(random_bytes(16)) : null;

	$created = [];
	$firstTransactionUID = null;
	
	foreach ($wineUIDs as $wineUID) {
		$wineUID = (int)$wineUID;
	
		$qty = isset($_POST['bottles'][$wineUID]) ? -(int)abs($_POST['bottles'][$wineUID]) : 0;
		$price = isset($_POST['price_per_bottle'][$wineUID]) ? (float)$_POST['price_per_bottle'][$wineUID] : 0.0;
	
		$transaction_fields = [
			'date'              => date('c'),
			'date_posted'       => empty($_POST['date_posted']) ? null : date('c', strtotime($_POST['date_posted'])),
			'username'          => $user->getUsername(),
			'type'              => 'Transaction',
			'wine_uid'          => $wineUID,
			'bottles'           => $qty,
			'price_per_bottle'  => $price,
			'name'              => $_POST['name'] ?? null,
			'description'       => $_POST['description'] ?? null,
			'linked'            => $linkedID
		];
	
		$transaction = new Transaction();
		$uid = $transaction->create($transaction_fields);
	
		if ($firstTransactionUID === null) {
			$firstTransactionUID = $uid; // capture the first created UID
		}
	
		$created[] = $wineUID;
	}
	
	if (!empty($created)) {
		echo json_encode([
			'success'   => true,
			'transaction_uid' => $firstTransactionUID // or the "main" UID for linked transactions
		]);
	} else {
		echo json_encode(['success' => false, 'message' => 'No valid wines to create transactions.']);
	}

} catch (Exception $e) {
	echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
