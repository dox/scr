<?php
declare(strict_types=1);
require_once '../inc/autoload.php';

$response = ['success' => false];
$log = $log ?? new Log();

$action = $_POST['action'] ?? '';
$log->add("Action received: $action", Log::INFO);

switch ($action) {
	case 'order_insert':
		// Basic order info
		$data = [
			'username'     => $user->getUsername(),
			'order_num'    => $_POST['order_num'] ?? null,
			'date_created'    => $_POST['date_created'] ?? null,
			'value'    => $_POST['value'] ?? null,
			'cost_centre'    => $_POST['cost_centre'] ?? null,
		];
		
		// Prepare items array from parallel inputs
		$names  = $_POST['itemName'] ?? [];
		$qtys   = $_POST['itemQty'] ?? [];
		$prices = $_POST['itemPrice'] ?? [];

		// Force arrays even for single items
		$names  = is_array($names) ? $names : [$names];
		$qtys   = is_array($qtys) ? $qtys : [$qtys];
		$prices = is_array($prices) ? $prices : [$prices];

		$items = [];
		$length = max(count($names), count($qtys), count($prices));

		for ($i = 0; $i < $length; $i++) {
			if (trim($names[$i]) === '') continue;
			$items[] = [
				'item_name'  => trim($names[$i]),
				'item_qty'   => (int)($qtys[$i] ?? 0),
				'item_value' => (float)($prices[$i] ?? 0),
			];
		}

		$data['items'] = $items; // JSON-encoded in your insert() method

		// Insert using your existing Order class
		$order = new Order();
		if ($order->insert($data)) {
			$response['success'] = true;
		} else {
			$response['error'] = 'Failed to insert order.';
		}
	break;
	
	case 'order_update':
		// Basic order info
		$data = [
			'id'     => $_POST['order_id'] ?? null,
			'username'     => $user->getUsername(),
			'order_num'    => $_POST['order_num'] ?? null,
			'date_created'    => $_POST['date_created'] ?? null,
			'value'    => $_POST['value'] ?? null,
			'cost_centre'    => $_POST['cost_centre'] ?? null,
		];
		
		// Prepare items array from parallel inputs
		$names  = $_POST['itemName'] ?? [];
		$qtys   = $_POST['itemQty'] ?? [];
		$prices = $_POST['itemPrice'] ?? [];
		
		// Force arrays even for single items
		$names  = is_array($names) ? $names : [$names];
		$qtys   = is_array($qtys) ? $qtys : [$qtys];
		$prices = is_array($prices) ? $prices : [$prices];
		
		$items = [];
		$length = max(count($names), count($qtys), count($prices));
		
		for ($i = 0; $i < $length; $i++) {
			if (trim($names[$i]) === '') continue;
			$items[] = [
				'item_name'  => trim($names[$i]),
				'item_qty'   => (int)($qtys[$i] ?? 0),
				'item_value' => (float)($prices[$i] ?? 0),
			];
		}
		
		$data['items'] = $items; // JSON-encoded in your insert() method
		
		// Insert using your existing Order class
		$order = new Order();
		if ($order->update($data)) {
			$response['success'] = true;
		} else {
			$response['error'] = 'Failed to update order.';
		}
	break;
	
	default:
		$response['error'] = "Unknown action: $action";
		break;
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
