<?php
class User {
	protected $ldapConn;
	protected $userData = [];
	protected $loggedIn = false;

	protected $ldapHost;
	protected $baseDn;

	public function __construct() {
		global $log;
		
		// LDAP settings from constants (you can define these in config.php)
		$this->ldapHost = defined('LDAP_HOST') ? LDAP_HOST : 'ldap://localhost';
		$this->baseDn   = defined('LDAP_BASE_DN') ? LDAP_BASE_DN : 'dc=example,dc=com';
		
		// Attempt connection
		$this->ldapConn = ldap_connect($this->ldapHost);
		if (!$this->ldapConn) {
			$log->add("Could not connect to LDAP server: {$this->ldapHost}", Log::ERROR);
			throw new Exception("Could not connect to LDAP server: {$this->ldapHost}");
		}

		// Recommended LDAP options
		ldap_set_option($this->ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ldapConn, LDAP_OPT_REFERRALS, 0);

		// Restore session if available
		if (isset($_SESSION['user'])) {
			$this->userData = $_SESSION['user'];
			$this->loggedIn = true;
		}
	}
	
	public function authenticate(string $username, string $password): bool {
		global $log;
		
		// Optional: bind with service account to search
		if (defined('LDAP_BIND_USER') && defined('LDAP_BIND_PASS')) {
			@ldap_bind($this->ldapConn, LDAP_BIND_USER, LDAP_BIND_PASS);
		}
	
		$filter = "(sAMAccountName={$username})"; // or sAMAccountName for AD
		$search = @ldap_search($this->ldapConn, $this->baseDn, $filter);
	
		if (!$search) {
			$log->add("Unable to search LDAP base: {$this->baseDn}", Log::ERROR);
			$this->logout();
			return false;
		}
		
		$entries = ldap_get_entries($this->ldapConn, $search);
		if ($entries['count'] == 0) {
			$log->add("No user found for: {$username}", Log::ERROR);
			$this->logout();
			return false;
		}
	
		$dn = $entries[0]['dn'];
		if (@ldap_bind($this->ldapConn, $dn, $password)) {
			$member = Member::fromLDAP($entries[0]['samaccountname'][0]);
			
			$this->userData = $entries[0];
			$this->loggedIn = true;
			$_SESSION['user'] = $this->userData;
			$_SESSION['user']['permissions'] = explode(",", $member->permissions);


			$log->add("User authenticated for: {$this->userData['samaccountname'][0]}", Log::INFO);

			return true;
		}
		
		$this->logout();
		return false;
	}
	
	public function isLoggedIn(): bool {
		return $this->loggedIn;
	}
	
	public function getUsername(): ?string {
		return strtoupper($this->userData['samaccountname'][0]) ?? null;
	}
	
	public function getEmail(): ?string {
		return $this->userData['mail'][0] ?? null;
	}
	
	public function getFullname(): ?string {
		return $this->userData['name'][0] ?? null;
	}
	
	public function isMemberOf(string $group): bool {
		if (!$this->isLoggedIn()) return false;
	
		if (empty($this->memberOf())) return false;
	
		$groups = $this->memberOf();
		
		if (in_array($group, $groups)) {
			return true;
		}
	
		return false;
	}
	
	public function memberOf(): array {
		if (!$this->isLoggedIn()) return [];
	
		if (!isset($this->userData['memberof'])) return [];
	
		$groups = $this->userData['memberof'];
	
		if (isset($groups['count'])) {
			unset($groups['count']);
		}
	
		return $groups;
	}
	
	public function available_permissions() {
		$availablePermissions = array(
			"global_admin" => "Access to all settings.  Overrides for all limits",
			"meals" => "Add/Edit/Delete meals",
			"bookings" => "Ability to override meal restrictions/cutoffs",
			"members" => "Add/Edit/Delete members",
			"impersonate" => "Ability to impersonate other members",
			"notifications" => "Add/Edit/Delete notifications",
			"terms" => "Add/Edit/Delete terms",
			"wine" => "Add/Edit/Delete wines",
			"reports" => "Add/Edit/Delete/Run reports",
			"settings" => "Add/Edit/Delete site settings",
			"logs" => "View logs"
		);
		
		return $availablePermissions;
	}
	
	public function hasPermission(string $permission): bool {
		// global admin always has everything
		if (in_array('global_admin', $_SESSION['user']['permissions'], true)) {
			return true;
		}
		return in_array($permission, $_SESSION['user']['permissions'], true);
	}
	
	public function pageCheck(string $permission): bool {
		// global admin always has everything
		if (in_array('global_admin', $_SESSION['user']['permissions'], true)) {
			return true;
		}
		if (in_array($permission, $_SESSION['user']['permissions'], true)) {
			return true;
		}
		
		die("You do not have access to this page.");
	}
	
	public function loggedOnTime() {
		global $db;
	
		$query = "SELECT TIMESTAMPDIFF(SECOND, MAX(date_created), NOW()) AS seconds_since_last_logon FROM new_logs WHERE username = ? AND type = 'INFO' AND event LIKE 'User authenticated%'";
		
		$row = $db->fetch($query, [$this->getUsername()]);
		
		if ($row) {
			return $row['seconds_since_last_logon'];
		}
		
		return null;
	}
	
	public function logout(): void {
		unset($_SESSION['user']);
		$this->userData = [];
		$this->loggedIn = false;
	}
}
