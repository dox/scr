<?php
class members {
	protected static $table_name = "members";
	
	public function getMembers($status = null, $type = null) {
		global $db;
		
		$sql = "SELECT * FROM " . self::$table_name;
		
		if (strtoupper($status) == "ENABLED") {
			$conditions[] = "enabled = '1'";
		} elseif (strtoupper($status) == "DISABLED") {
			$conditions[] = "enabled = '0'";
		}
		
		if (in_array(strtoupper($type), ['SCR', 'MCR'])) {
			$conditions[] = "type = '" . strtoupper($type) . "'";
		}
		
		if (!empty($conditions)) {
			$sql .= " WHERE " . implode(" AND ", $conditions);
		}
		
		$sql .= " ORDER BY precedence ASC, lastname ASC, firstname ASC";
		
		$rows = $db->query($sql)->fetchAll();
		
		$members = [];
		
		foreach ($rows as $row) {
			$member = new member($row['uid']);
			$members[] = $member;
		}
		
		return $members;
	}

	public function memberCategories() {
		global $settingsClass;
		
		$raw = $settingsClass->value('member_categories');
		$memberCategoriesSettings = array_filter(
			array_map('trim', explode(',', $raw)),
			fn($v) => $v !== ''
		);
		
		// Always start with mandatory empty string
		$memberCategories = array_merge([''], $memberCategoriesSettings);
		
		return $memberCategories;
	}
	
	public function memberTitles() {
		global $settingsClass;
		
		$raw = $settingsClass->value('member_titles');
		$memberTitlesSettings = array_filter(
			array_map('trim', explode(',', $raw)),
			fn($v) => $v !== ''
		);
		
		// Always start with mandatory empty string
		$memberTitles = array_merge([''], $memberTitlesSettings);
		
		return $memberTitles;
	}
	
	public function dietaryOptions() {
		global $settingsClass;
		
		$raw = $settingsClass->value('meal_dietary');
		$dietaryOptionsSettings = array_filter(
			array_map('trim', explode(',', $raw)),
			fn($v) => $v !== ''
		);
		
		// Sort dietary options alphabetically
		sort($dietaryOptionsSettings);
		
		return $dietaryOptionsSettings;
	}
}
?>