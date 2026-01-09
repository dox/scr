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
	$transactionUID = filter_input(INPUT_POST, 'uid', FILTER_VALIDATE_INT);
	if (empty($transactionUID)) {
		echo json_encode(['success' => false, 'message' => 'No transaction UID provided.']);
		exit;
	}
	
	$transaction = new Transaction($transactionUID);
	if ($transaction->isLinked()) {
		$transactions = $transaction->linkedTransactions();
	} else {
		$transactions[] = $transaction;
	}
	
	$deleted = [];
	foreach ($transactions as $transactionToDelete) {
		$uid = $transactionToDelete->uid;
		$transactionToDelete->delete();
	
		$deleted[] = $uid;
	}
	
	if (!empty($deleted)) {
		echo json_encode(['success' => true, 'message' => count($deleted) . ' transactions deleted.']);
	} else {
		echo json_encode(['success' => false, 'message' => 'No valid transactions were deleted.']);
	}

} catch (Exception $e) {
	echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
