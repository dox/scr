<?php

use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\ActiveDirectory\User as AdUser;

class User {
	protected $userData = [];
	protected $loggedIn = false;

	const COOKIE_NAME     = 'scr_user_token';
	const COOKIE_LIFETIME = 2592000; // 30 days

	public function __construct() {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		// Existing session?
		if (!empty($_SESSION['user'])) {
			$this->userData = $_SESSION['user'];
			$this->loggedIn = true;
			return;
		}

		$this->setupLdap();
		$this->tryTokenRestore();
	}

	protected function setupLdap(): void {
		$connection = new Connection([
			'hosts'    => [LDAP_HOST],
			'base_dn'  => LDAP_BASE_DN,
			'username' => LDAP_BIND_USER,
			'password' => LDAP_BIND_PASS,
			'use_tls'  => LDAP_USE_TLS,
		]);

		Container::addConnection($connection);
	}

	private function finalizeLogin(Member $member, bool $remember = false, bool $viaCookie = false): void {
		global $db, $log;
	
		// Update last login
		$db->update(
			'members',
			['date_lastlogon' => date('c')],
			['uid' => $member->uid],
			false
		);
	
		$this->userData = [
			'samaccountname' => $member->ldap,
			'type'          => $member->type ?? null,
			'category'      => $member->category ?? null,
			'name'          => $member->name() ?? null,
			'email'         => $member->email ?? null,
			'permissions'  => explode(',', $member->permissions ?? ''),
			'uid'           => $member->uid
		];
	
		$_SESSION['user'] = $this->userData;
		$this->loggedIn   = true;
	
		if ($remember) {
			$this->setToken($member->uid);
		}
	
		$suffix = $viaCookie ? ' (with cookie)' : '';
		$log->add("{$member->ldap} ({$member->name()}) authenticated{$suffix}", 'auth', Log::SUCCESS);
	
		if ($viaCookie) {
			toast('Login Successful', 'Login successful via stored cookie', 'text-success');
		}
	}
	
	private function tryTokenRestore(): bool {
		global $db, $log;
	
		if (empty($_COOKIE[self::COOKIE_NAME])) {
			return false;
		}
	
		$token = $_COOKIE[self::COOKIE_NAME];
	
		$record = $db->fetch("
			SELECT member_uid, token_expiry
			FROM tokens
			WHERE token = ?
			LIMIT 1
		", [$token]);
	
		if (!$record || strtotime($record['token_expiry']) < time()) {
			$db->delete('tokens', ['token' => $token], false);
			return false;
		}
	
		$member = Member::fromUID($record['member_uid']);
		if (!$member) {
			error_log("Error: Failed login attempt for member UID: {$record['member_uid']} from {$_SERVER['REMOTE_ADDR']}");
			return false;
		}
	
		$this->finalizeLogin($member, false, true);
		return true;
	}
	
	public function authenticate(string $username, string $password, bool $remember = false): bool {
		global $log;
	
		try {
			$user = AdUser::whereEquals('samaccountname', $username)->firstOrFail();
		} catch (\Exception $e) {
			$log->add("LDAP user not found: {$username}", 'auth', Log::WARNING);
			error_log("Error: Failed login attempt from {$_SERVER['REMOTE_ADDR']}");
			$this->logout();
			return false;
		}
	
		$connection = $user->getConnection();
	
		if (!$connection->auth()->attempt($user->getDn(), $password)) {
			$log->add("Invalid credentials for: {$username}", 'auth', Log::WARNING);
			error_log("Error: Failed login attempt from {$_SERVER['REMOTE_ADDR']}");
			$this->logout();
			return false;
		}
	
		$member = Member::fromLDAP($user->samaccountname[0]);
		if (!$member) {
			$log->add("Member DB record missing: {$username}", 'auth', Log::WARNING);
			error_log("Error: Failed login attempt from {$_SERVER['REMOTE_ADDR']}");
			$this->logout();
			return false;
		}
	
		$this->finalizeLogin($member, $remember);
		return true;
	}

	protected function setToken(int $memberUID): void {
		global $db;
	
		$token = bin2hex(random_bytes(32));
		$expiry = (new DateTime('+1 month'))->format('Y-m-d H:i:s');
	
		$db->query("DELETE FROM tokens WHERE token_expiry < NOW()");
		$db->query("
			INSERT INTO tokens (member_uid, token, token_expiry)
			VALUES (?, ?, ?)
		", [$memberUID, $token, $expiry]);
	
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
			$db->delete('tokens', ['token' => $_COOKIE[self::COOKIE_NAME]], false);

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
	}

	// Permissions
	public function hasPermission(string $permission): bool {
		if (!isset($_SESSION['user']['permissions'])) {
			return false;
		}
		if (in_array('global_admin', $_SESSION['user']['permissions'], true)) {
			return true;
		}
		return in_array($permission, $_SESSION['user']['permissions'], true);
	}

	public function pageCheck(string $permission): bool {
		if ($this->hasPermission('global_admin') || $this->hasPermission($permission)) {
			return true;
		}
		
		error_log("Error: Failed page access attempt from {$_SERVER['REMOTE_ADDR']}");
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

	public function getUID(): ?int {
		return $this->userData['uid'] ?? null;
	}
	
	public function getUsername(): ?string {
		return $this->userData['samaccountname'] ?? null;
	}
}
