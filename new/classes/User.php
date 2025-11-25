<?php
class User {
	protected $ldapConn;
	protected $userData = [];
	protected $loggedIn = false;
	protected $uid = null;

	protected $ldapHost;
	protected $baseDn;

	const COOKIE_NAME     = 'scr_user_token';
	const COOKIE_LIFETIME = 2592000; // 30 days

	public function __construct() {
		global $db;

		$this->ldapHost = LDAP_HOST ?? 'ldap://localhost';
		$this->baseDn   = LDAP_BASE_DN ?? 'dc=example,dc=com';

		$this->ldapConn = @ldap_connect($this->ldapHost);
		ldap_set_option($this->ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ldapConn, LDAP_OPT_REFERRALS, 0);

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		// 1️⃣ Existing session user
		if (!empty($_SESSION['user'])) {
			$this->userData = $_SESSION['user'];
			$this->loggedIn = true;
			$this->uid      = $_SESSION['user']['uid'] ?? null;
			return;
		}

		// 2️⃣ Try token restoration
		$this->tryTokenRestore();
	}

	private function tryTokenRestore(): bool {
		global $db;

		if (empty($_COOKIE[self::COOKIE_NAME])) {
			error_log("No token cookie found.");
			return false;
		}

		$token = $_COOKIE[self::COOKIE_NAME];
			
		$record = $db->fetch("
			SELECT member_uid, token_expiry
			FROM tokens
			WHERE token = ?
			LIMIT 1
		", [$token]);

		if (!$record) {
			error_log("Token not found in database.");
			return false;
		}
		
		// delete token if expired
		if (strtotime($record['token_expiry']) < time()) {
			$db->delete(
				'tokens',
				['token' => $token],
				false
			);
			
			return false;
		}

		$member = Member::fromUID($record['member_uid']); // returns object with username, permissions, uid
		if (!$member) {
			error_log("Member not found for UID {$record['member_uid']}");
			return false;
		}
		
		$this->uid = $member->uid;
		$this->userData = [
			'samaccountname' => $member->ldap, // string
			'permissions'    => explode(',', $member->permissions ?? ''),
			'uid'     => $member->uid
		];

		$_SESSION['user'] = $this->userData;
		$this->loggedIn = true;

		error_log("User restored from token: {$member->ldap}");

		return true;
	}

	public function authenticate(string $username, string $password, bool $remember = false): bool {
		global $log, $db;

		// Bind with service account
		@ldap_bind($this->ldapConn, LDAP_BIND_USER, LDAP_BIND_PASS);

		$filter = "(sAMAccountName={$username})";
		$search = @ldap_search($this->ldapConn, $this->baseDn, $filter);

		if (!$search) {
			$log->add("LDAP search failed for: {$username}", Log::ERROR);
			$this->logout();
			return false;
		}

		$entries = @ldap_get_entries($this->ldapConn, $search);
		if (!$entries || ($entries['count'] ?? 0) === 0) {
			$log->add("No LDAP entry for: {$username}", Log::ERROR);
			$this->logout();
			return false;
		}

		$dn = $entries[0]['dn'] ?? null;
		if (!$dn) {
			$log->add("DN missing for: {$username}", Log::ERROR);
			$this->logout();
			return false;
		}

		if (!@ldap_bind($this->ldapConn, $dn, $password)) {
			$log->add("Invalid credentials for: {$username}", Log::ERROR);
			$this->logout();
			return false;
		}

		// Successful authentication
		$member = Member::fromLDAP($entries[0]['samaccountname'][0]);
		if (!$member) {
			$log->add("Member record not found for: {$username}", Log::ERROR);
			$this->logout();
			return false;
		}

		$this->uid = $member->uid;
		$this->userData = [
			'samaccountname' => $member->ldap,
			'permissions'    => explode(',', $member->permissions ?? ''),
			'uid'     => $member->uid
		];

		$_SESSION['user'] = $this->userData;
		$this->loggedIn = true;

		if ($remember == 1) {
			$this->setToken($member->uid);
		}

		$log->add("User authenticated: {$member->ldap}", Log::INFO);

		return true;
	}

	protected function setToken(int $memberUID): void {
		global $db;
	
		$token = bin2hex(random_bytes(32));
	
		// Calculate expiry date 1 month from now
		$expiryDate = (new DateTime('+1 month'))->format('Y-m-d H:i:s');
	
		// Delete any old tokens
		$db->query("DELETE FROM tokens WHERE token_expiry < NOW()");
	
		// Insert new token with expiry
		$db->query("
			INSERT INTO tokens (member_uid, token, token_expiry)
			VALUES (?, ?, ?)
		", [$memberUID, $token, $expiryDate]);
	
		setcookie(self::COOKIE_NAME, $token, [
			'expires'  => time() + self::COOKIE_LIFETIME,
			'path'     => '/',
			'secure'   => true,
			'httponly' => true,
			'samesite' => 'Strict'
		]);
	}

	public function logout(): void {
		global $db;

		if (!empty($_COOKIE[self::COOKIE_NAME])) {
			$db->delete(
				'tokens',
				['token' => $_COOKIE[self::COOKIE_NAME]],
				false
			);

			setcookie(self::COOKIE_NAME, '', [
				'expires'  => time() - 3600,
				'path'     => '/',
				'secure'   => true,
				'httponly' => true,
				'samesite' => 'Strict'
			]);
		}

		unset($_SESSION['user']);
		unset($_SESSION['impersonation_backup']);
		$this->loggedIn = false;
		$this->userData = [];
		$this->uid = null;
	}

	// Permissions
	public function hasPermission(string $permission): bool {
		if (!isset($_SESSION['user']['permissions'])) return false;
		if (in_array('global_admin', $_SESSION['user']['permissions'], true)) return true;
		return in_array($permission, $_SESSION['user']['permissions'], true);
	}

	public function pageCheck(string $permission): bool {
		if ($this->hasPermission('global_admin') || $this->hasPermission($permission)) {
			return true;
		}
		die("You do not have access to this page.");
	}

	public function available_permissions(): array {
		return [
			"global_admin"   => "Access to all settings. Overrides all limits",
			"meals"          => "Add/Edit/Delete meals",
			"bookings"       => "Override restrictions",
			"members"        => "Add/Edit/Delete members",
			"impersonate"    => "Impersonate other members",
			"notifications"  => "Add/Edit/Delete notifications",
			"terms"          => "Add/Edit/Delete terms",
			"wine"           => "Add/Edit/Delete wines",
			"reports"        => "Run reports and manage report settings",
			"settings"       => "Add/Edit/Delete site settings",
			"logs"           => "View logs"
		];
	}

	public function isLoggedIn(): bool {
		return $this->loggedIn;
	}

	public function getUsername(): ?string {
		return $this->userData['samaccountname'] ?? null;
	}
}