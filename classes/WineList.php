<?php
class WineList extends Model {
	public $uid;
	public $name;
	public $type;
	public $member_ldap;
	public $wine_uids;
	public $notes;
	public $last_updated;
	
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
	
	public function listItem(?int $currentWineUid = null): string {
		$wineUIDs = array_map('intval', $this->wineUIDs());
	
		$isInCurrentWine = $currentWineUid !== null
			&& in_array($currentWineUid, $wineUIDs, true);
	
		$heartClass = $isInCurrentWine ? 'bi-heart-fill' : 'bi-heart';
	
		$wineUidAttr = $currentWineUid !== null
			? ' data-wine-uid="' . $currentWineUid . '"'
			: '';
	
		$heartHtml  = '<span class="wine-heart"' . $wineUidAttr;
		$heartHtml .= ' data-list-uid="' . (int)$this->uid . '" style="cursor:pointer;">';
		$heartHtml .= '<i class="bi ' . $heartClass . ' text-danger me-2"></i>';
		$heartHtml .= '</span>';
	
		// List type badge
		$typeBadge = '';
		if ($this->type === 'public') {
			$typeBadge = '<span class="badge bg-success ms-2">Public</span>';
		} elseif ($this->type === 'private') {
			$typeBadge = '<span class="badge bg-primary ms-2">Private</span>';
		}
	
		// Wine count
		$wineCount = count($wineUIDs);
	
		// Build filter URL (guard against empty list)
		$url = $wineUIDs
			? 'index.php?page=wine_filter'
				. '&conditions[0][field]=wine_wines.uid'
				. '&conditions[0][operator]=IN'
				. '&conditions[0][value]=' . implode(',', $wineUIDs)
			: '#';
	
		// Count badge AS LINK
		$countBadge = $wineCount
			? '<a href="' . $url . '" class="badge bg-secondary ms-2 text-decoration-none">'
				. $wineCount . ' wine' . ($wineCount !== 1 ? 's' : '')
				. '</a>'
			: '<span class="badge bg-secondary ms-2">0 wines</span>';
	
		// Last updated
		$updatedText = $this->last_updated
			? '<small class="text-muted d-block">Last updated: '
				. formatDate($this->last_updated, 'short') . ' '
				. formatTime($this->last_updated)
				. '</small>'
			: '';
	
		// Render list item
		$html  = '<div class="list-group-item d-flex justify-content-between align-items-start">';
		$html .= '<div class="d-flex align-items-center">';
		$html .= $heartHtml;
		$html .= '<div>';
		$html .= '<div class="fw-bold">'
			   . htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8')
			   . '</div>';
		$html .= $updatedText;
		$html .= '</div>';
		$html .= '</div>'; // left
		$html .= '<div class="text-end">';
		$html .= $typeBadge . $countBadge;
		$html .= '</div>';
		$html .= '</div>';
	
		return $html;
	}
	
	public function listItemEdit(): string {
		$uid = (int) $this->uid;
		$name = htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8');
	
		// URLs
		$filterUrl = 'index.php?page=wine_filter'
				   . '&conditions[0][field]=wine_wines.uid'
				   . '&conditions[0][operator]=IN'
				   . '&conditions[0][value]=' . implode(',', $this->wineUIDs());
		$editUrl = 'index.php?page=wine_list_edit&uid=' . $uid;
	
		// Wine count
		$wineCount = count($this->wines());
		$wineLabel = $wineCount === 1 ? 'wine' : 'wines';
	
		// Count badge as link
		$countBadge = $wineCount
			? '<a href="' . $filterUrl . '" class="badge bg-secondary ms-2 text-decoration-none">'
				. $wineCount . ' ' . $wineLabel
				. '</a>'
			: '<span class="badge bg-secondary ms-2">0 wines</span>';
	
		// Last updated
		$updatedText = $this->last_updated
			? '<small class="text-muted d-block">Last updated: '
				. formatDate($this->last_updated, 'short') . ' '
				. formatTime($this->last_updated)
				. '</small>'
			: '';
	
		// Render output
		$output = '';
		$output .= '<div class="list-group-item d-flex justify-content-between align-items-start">';
		$output .= '<div>';
		$output .= '<div class="fw-bold">' . $name . '</div>';
		$output .= $updatedText;
		$output .= '</div>'; // left content
	
		$output .= '<div class="d-flex align-items-center gap-2">';
		$output .= $countBadge;
		$output .= '<a href="' . $editUrl . '" class="btn btn-sm btn-link" title="Edit wine list">';
		$output .= '<i class="bi bi-pencil"></i>';
		$output .= '</a>';
		$output .= '<button type="button" class="btn btn-sm btn-link text-danger js-delete-wine-list" data-list-uid="' . $uid . '" title="Delete wine list">';
		$output .= '<i class="bi bi-trash"></i>';
		$output .= '</button>';
		$output .= '</div>'; // right content
	
		$output .= '</div>'; // list-group-item
	
		return $output;
	}
	
	public function update(array $postData) {
		global $db;
	
		// Map normal text/select fields
		$fields = [
			'name'      => $postData['name'] ?? null,
			'type'  => $postData['type'] ?? null,
			'notes'   => $postData['notes'] ?? null
		];
	
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
		
		toast('List Updated', 'List sucesfully updated', 'text-success');
	
		return $updatedRows;
	}
	
	public function delete() {
		global $db;
		if (!isset($this->uid)) {
			return false;
		}
		
		$db->delete(
			static::$table,
			['uid' => $this->uid],
			'logs'
		);
		
		toast('List Deleted', 'List sucesfully deleted', 'text-success');
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
