<?php
class WineList extends Model {
	public $uid;
	private $name;
	private $type;
	private $member_ldap;
	private $wine_uids;
	private $notes;
	private $last_updated;
	
	protected $db;
	protected static string $table = 'wine_lists';
	
	public function __construct($uid = null) {
		$this->db = Database::getInstance();
	
		if ($uid !== null) {
			$this->getOne($uid);
		}
	}
	
	public function getOne($uid) {
		$query = "SELECT * FROM " . static::$table . " WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);
	
		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}
	
	private function wineUIDs(): array{
		// Convert the stored CSV into an array (if empty, fall back to [])
		$wineUIDs = $this->wine_uids
			? array_map('trim', explode(',', $this->wine_uids))
			: [];
		
		return $wineUIDs;
	}
	
	public function wines(): array{
		$wines = [];
		
		foreach ($this->wineUIDs() as $uid) {
			$wines[] = new Wine($uid);
		}
		
		return $wines;
	}
	
	public function listItem($currentWineUid = null): string {
		// Determine if the current wine is in this list
		$isInCurrentWine = $currentWineUid && in_array($currentWineUid, $this->wineUIDs());
	
		// Heart icon (empty or full) with data attributes
		$heartClass = $isInCurrentWine ? 'bi-heart-fill' : 'bi-heart';
		$heartHtml = '<span class="wine-heart" data-wine-uid="' . htmlspecialchars($currentWineUid) . '" data-list-uid="' . htmlspecialchars($this->uid) . '" style="cursor:pointer;">';
		$heartHtml .= '<i class="bi ' . $heartClass . ' text-danger me-2"></i>';
		$heartHtml .= '</span>';
	
		// Badge for list type
		$typeBadge = '';
		if ($this->type === 'public') {
			$typeBadge = '<span class="badge bg-success ms-2">Public</span>';
		} elseif ($this->type === 'mine') {
			$typeBadge = '<span class="badge bg-primary ms-2">Mine</span>';
		}
	
		// Wine count badge
		$wineCount = count($this->wineUIDs());
		$countBadge = '<span class="badge bg-secondary ms-2">' . $wineCount . ' wine' . ($wineCount !== 1 ? 's' : '') . '</span>';
	
		// Last updated
		$updatedText = $this->last_updated 
			? '<small class="text-muted d-block">Last updated: ' . formatDate($this->last_updated, 'short') . ' ' . formatTime($this->last_updated) . '</small>' 
			: '';
	
		// Build filter URL
		$url = "index.php?page=wine_filter&conditions[0][field]=wine_wines.uid&conditions[0][operator]=IN&conditions[0][value]=" . implode(',', $this->wineUIDs());
	
		// Render as <a> with Bootstrap classes
		$html = '<a href="' . $url . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">';
		$html .= '<div class="d-flex align-items-center">';
		$html .= $heartHtml;
		$html .= '<div>';
		$html .= '<div class="fw-bold">' . htmlspecialchars($this->name) . '</div>';
		$html .= $updatedText;
		$html .= '</div>';
		$html .= '</div>'; // end left content
		$html .= '<div class="text-end">';
		$html .= $typeBadge . $countBadge;
		$html .= '</div>';
		$html .= '</a>';
	
		return $html;
	}
	
	/**
	 * Toggle a wine UID in/out of this wine list.
	 * Returns an array with keys:
	 *   - success (bool)
	 *   - action ('added' or 'removed')
	 *   - wine_count (int)
	 */
	public function toggle(string $wineUid): array {
		global $db;
	
		if (!$this->uid) {
			return ['success' => false, 'message' => 'Invalid list'];
		}
	
		$currentWines = $this->wineUIDs(); // array of current wine UIDs
	
		if (in_array($wineUid, $currentWines)) {
			// Remove the wine
			$currentWines = array_values(array_filter($currentWines, fn($uid) => $uid != $wineUid));
			$action = 'removed';
		} else {
			// Add the wine
			$currentWines[] = $wineUid;
			$action = 'added';
		}
	
		// Save back as CSV
		$this->wine_uids = implode(',', $currentWines);
	
		try {
			$affected = $db->update(
				static::$table,
				['wine_uids' => $this->wine_uids, 'last_updated' => date('Y-m-d H:i:s')],
				['uid' => $this->uid]
			);
	
			return [
				'success' => true,
				'action' => $action,
				'wine_count' => count($currentWines)
			];
		} catch (Exception $e) {
			return ['success' => false, 'message' => $e->getMessage()];
		}
	}

}
