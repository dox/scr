<?php
$argv = array("--dy-run");

// CONFIG - edit if your table / pk / column names differ
$table = 'meals';
$pk = 'uid';                 // primary key column name used by Database::update() WHERE
$capacityColumn = 'capacity';// JSON column to write
$selectCols = ['scr_capacity', 'scr_dessert_capacity', 'scr_guests'];
$dryRun = in_array('--dry-run', $argv); // pass --dry-run to see output without changing DB

$db = Database::getInstance();

try {
	// fetch rows (adjust WHERE if you only want to update subset)
	$colsSql = implode(', ', array_map(fn($c) => "`$c`", $selectCols));
	$rows = $db->fetchAll("SELECT `$pk`, $colsSql FROM `$table`");

	if (empty($rows)) {
		echo "No rows found in table {$table}.\n";
		exit(0);
	}

	echo "Found " . count($rows) . " rows. " . ($dryRun ? "Dry-run mode (no DB writes).\n" : "Will perform updates.\n");

	$db->beginTransaction();
	$updated = 0;
	foreach ($rows as $row) {
		$id = $row[$pk];

		// Read values and coerce to int (use 0 fallback if null/empty)
		$main = isset($row['scr_capacity']) && $row['scr_capacity'] !== null && $row['scr_capacity'] !== '' 
			? (int)$row['scr_capacity'] : 0;
		$dessert = isset($row['scr_dessert_capacity']) && $row['scr_dessert_capacity'] !== null && $row['scr_dessert_capacity'] !== ''
			? (int)$row['scr_dessert_capacity'] : 0;
		$guests = isset($row['scr_guests']) && $row['scr_guests'] !== null && $row['scr_guests'] !== ''
			? (int)$row['scr_guests'] : 0;

		// Build JSON structure exactly as you provided, populating SCR from DB
		$capacityArr = [
			"SCR" => [
				"seating" => [
					"main" => $main,
					"dessert" => $dessert
				],
				"guests" => [
					"max_per_member" => $guests
				]
			],
			// static defaults taken from your sample JSON
			"Staff" => [
				"seating" => [
					"main" => 0,
					"dessert" => 0
				],
				"guests" => [
					"max_per_member" => 0
				]
			],
			"Student" => [
				"seating" => [
					"main" => 0,
					"dessert" => 0
				],
				"guests" => [
					"max_per_member" => 0
				]
			]
		];

		// encoded JSON (store as string; if your DB column is JSON type this will work)
		$json = json_encode($capacityArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		if ($dryRun) {
			echo "UID {$id} -> " . $json . "\n";
		} else {
			// Use your Database::update method
			$affected = $db->update($table, [$capacityColumn => $json], [$pk => $id], /*logChanges=*/false);
			// update returns number of affected rows
			if ($affected > 0) $updated++;
		}
	}

	if (!$dryRun) {
		$db->commit();
		echo "Done. Rows updated: {$updated}\n";
	} else {
		$db->rollBack(); // nothing written; just rollback to be safe
		echo "Dry-run finished. No changes were made.\n";
	}

} catch (Exception $e) {
	try { $db->rollBack(); } catch (Exception $_) {}
	echo "Error: " . $e->getMessage() . "\n";
	exit(1);
}


?>
