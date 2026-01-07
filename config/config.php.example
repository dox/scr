<?php
/**
 * Global configuration file
 * Defines constants for database and environment settings.
 */

# -------------------------
# Database settings
# -------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'databasename');
define('DB_USER', 'username');
define('DB_PASS', 'password');

# -------------------------
# LDAP settings
# -------------------------
define('LDAP_HOST', 'ldap_host');
define('LDAP_BASE_DN', 'base_dn');
define('LDAP_BIND_USER', 'bind_user');
define('LDAP_BIND_PASS', 'bind_password');
define('LDAP_USE_TLS', false);
define('LDAP_USERNAME_ATTRIBUTE', 'sAMAccountName'); // LDAP username attribute (uid for OpenLDAP, sAMAccountName for AD)

# -------------------------
# SMTP settings
# -------------------------
define('SMTP_SERVER', 'smtp_server');
define('SMTP_AUTH', false);
define('SMTP_USERNAME', false);
define('SMTP_PASSWORD', false);
define('SMTP_PORT', '587');
define('SMTP_SENDER_ADDRESS', 'smtp_sender@example.com');
define('SMTP_SENDER_NAME', 'SCR Booking System');

# -------------------------
# Application settings
# -------------------------
define('APP_NAME', 'SCR Meal Booking');
define('APP_URL', 'https://url');
define('APP_DEBUG', true);
define('BASE_PATH', realpath(__DIR__ . '/..'));
define('UPLOAD_DIR', BASE_PATH . '/uploads/');
//define('UPLOADS_INVOICES', STORAGE_PATH . '/uploads/invoices');
//define('BUDGET_START_MONTH', 8); // 8 = August
define('RESET_URL', 'https://reseturl');
define('COOKIE_SALT', 'RANDOM_SALT');
define('LOCKOUT_COUNT', 10);
