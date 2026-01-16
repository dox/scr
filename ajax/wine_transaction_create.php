<?php
header('Content-Type: application/json');

require_once '../inc/autoload.php';

try {

	// ----------------------------
	// Only allow POST
	// ----------------------------
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
		exit;
	}

	$transactionType = $_POST['transaction_type'] ?? 'invoice';

	// ============================================================
	// STOCK ADJUSTMENT TRANSACTION
	// ============================================================
	if ($transactionType === 'stock_adjustment') {

		$wineUID = (int)($_POST['wine_uid'] ?? 0);
		if ($wineUID <= 0) {
			echo json_encode(['success' => false, 'message' => 'Invalid wine selected.']);
			exit;
		}

		$adjustment = (int)($_POST['adjustment'] ?? 0);
		if ($adjustment === 0) {
			echo json_encode(['success' => false, 'message' => 'Adjustment quantity is required.']);
			exit;
		}

		$type = $_POST['type'] ?? 'decrease';
		$qty  = ($type === 'increase')
			? abs($adjustment)
			: -abs($adjustment);

		$wine = new Wine($wineUID);
		$bin = new Bin($wine->bin_uid);

		$transaction_fields = [
			'date'              => date('c'),
			'date_posted'       => date('c'),
			'username'          => $user->getUsername(),
			'type'              => 'Stock Adjustment',
			'name'              => 'Stock Adjustment',
			'wine_uid'          => $wine->uid,
			'cellar_uid'        => $bin->cellar_uid,
			'bottles'           => $qty,
			'price_per_bottle'  => 0,
			'description'       => $_POST['notes'] ?? null,
			'linked'            => null,
			'snapshot'          => json_encode($wine)
		];

		$transaction = new Transaction();
		$uid = $transaction->create($transaction_fields);

		echo json_encode([
			'success' => true,
			'transaction_uid' => $uid
		]);
		exit;
	}

	// ============================================================
	// INVOICE / STANDARD TRANSACTIONS (existing behaviour)
	// ============================================================

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

		$wine = new Wine($wineUID);
		$bin = new Bin($wine->bin_uid);

		$qty = isset($_POST['bottles'][$wine->uid])
			? -(int)abs($_POST['bottles'][$wine->uid])
			: 0;

		$price = isset($_POST['price_per_bottle'][$wine->uid])
			? (float)$_POST['price_per_bottle'][$wine->uid]
			: 0.0;

		$transaction_fields = [
			'date'              => date('c'),
			'date_posted'       => empty($_POST['date_posted'])
									? null
									: date('c', strtotime($_POST['date_posted'])),
			'username'          => $user->getUsername(),
			'type'              => 'Transaction',
			'wine_uid'          => $wine->uid,
			'cellar_uid'        => $bin->cellar_uid,
			'bottles'           => $qty,
			'price_per_bottle'  => $price,
			'name'              => $_POST['name'] ?? null,
			'description'       => $_POST['description'] ?? null,
			'linked'            => $linkedID,
			'snapshot'          => json_encode($wine)
		];

		$transaction = new Transaction();
		$uid = $transaction->create($transaction_fields);

		if ($firstTransactionUID === null) {
			$firstTransactionUID = $uid;
		}

		$created[] = $wine->uid;
	}

	if (!empty($created)) {
		echo json_encode([
			'success' => true,
			'transaction_uid' => $firstTransactionUID
		]);
	} else {
		echo json_encode([
			'success' => false,
			'message' => 'No valid wines to create transactions.'
		]);
	}

} catch (Exception $e) {
	echo json_encode([
		'success' => false,
		'message' => 'Server error: ' . $e->getMessage()
	]);
}
