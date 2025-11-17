<?php
class User {
	protected $ldapConn;
	protected $userData = [];
	protected $loggedIn = false;

	protected $ldapHost;
	protected $baseDn;

	// cookie settings
	const COOKIE_NAME = 'scr_user_token';
	const COOKIE_LIFETIME = 2592000; // 30 days

	public function __construct() {
		global $log;

		$this->ldapHost = defined('LDAP_HOST') ? LDAP_HOST : 'ldap://localhost';
		$this->baseDn   = defined('LDAP_BASE_DN') ? LDAP_BASE_DN : 'dc=example,dc=com';

		$this->ldapConn = @ldap_connect($this->ldapHost);
		ldap_set_option($this->ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ldapConn, LDAP_OPT_REFERRALS, 0);

		if (isset($_SESSION['user'])) {
			$this->userData = $_SESSION['user'];
			$this->loggedIn = true;
			return;
		}

		// Try cookie
		if (!empty($_COOKIE[self::COOKIE_NAME])) {
			$decoded = json_decode($_COOKIE[self::COOKIE_NAME], true);

			if (is_array($decoded) && isset($decoded['user'], $decoded['hash'])) {
				$expected = hash_hmac('sha256', $decoded['user'], COOKIE_SALT);
				if (hash_equals($decoded['hash'], $expected)) {
					// restore light profile
					$this->userData = ['samaccountname' => [ $decoded['user'] ]];
					$this->loggedIn = true;
					$_SESSION['user'] = $this->userData;

					$log->add("Cookie authentication restored for {$decoded['user']}", Log::INFO);
				}
			}
		}
	}

	public function authenticate(string $username, string $password, bool $remember_me): bool {
		global $log;

		// optional service bind for search
		$prev = set_error_handler(function() { return true; });
		if (defined('LDAP_BIND_USER') && defined('LDAP_BIND_PASS')) {
			@ldap_bind($this->ldapConn, LDAP_BIND_USER, LDAP_BIND_PASS);
		}
		restore_error_handler();

		$filter = "(sAMAccountName={$username})";

		$prev = set_error_handler(function() { return true; });
		$search = @ldap_search($this->ldapConn, $this->baseDn, $filter);
		restore_error_handler();

		if (!$search) {
			$log->add("LDAP search failed for: {$username}", Log::ERROR);
			$this->logout();
			return false;
		}

		$prev = set_error_handler(function() { return true; });
		$entries = @ldap_get_entries($this->ldapConn, $search);
		restore_error_handler();

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

		// quiet bind test
		$prev = set_error_handler(function() { return true; });
		$bindOk = @ldap_bind($this->ldapConn, $dn, $password);
		restore_error_handler();

		if (!$bindOk) {
			$log->add("Invalid credentials for: {$username}", Log::ERROR);
			$this->logout();
			return false;
		}

		// success
		$this->userData = $entries[0];
		$this->loggedIn = true;

		$_SESSION['user'] = $this->userData;

		// Set permissions
		$member = @Member::fromLDAP($entries[0]['samaccountname'][0]);
		$_SESSION['user']['permissions'] = explode(",", $member->permissions ?? "");

		$log->add("User authenticated for: {$this->getUsername()}", Log::INFO);

		// remember me cookie
		if ($remember_me) {
			$this->setCookieToken(strtolower($username));
		}

		return true;
	}

	protected function setCookieToken(string $username): void {
		$payload = [
			'user' => $username,
			'hash' => hash_hmac('sha256', $username, COOKIE_SALT)
		];
		setcookie(self::COOKIE_NAME, json_encode($payload), [
			'expires'  => time() + self::COOKIE_LIFETIME,
			'path'     => '/',
			'secure'   => true,
			'httponly' => true,
			'samesite' => 'Strict'
		]);
	}

	public function logout(): void {
		unset($_SESSION['user']);
		$this->loggedIn = false;
		$this->userData = [];

		setcookie(self::COOKIE_NAME, '', [
			'expires' => time() - 3600,
			'path'    => '/',
			'secure'  => true,
			'httponly'=> true,
			'samesite'=> 'Strict'
		]);
	}

	public function isLoggedIn(): bool {
		return $this->loggedIn;
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

	public function getUsername(): ?string {
		return isset($this->userData['samaccountname'][0])
			? strtoupper($this->userData['samaccountname'][0])
			: null;
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
		if (!isset($_SESSION['user']['permissions'])) return false;
		if (in_array('global_admin', $_SESSION['user']['permissions'], true)) return true;
		return in_array($permission, $_SESSION['user']['permissions'], true);
	}
}
