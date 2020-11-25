<?php
DEFINE("debug", false);

DEFINE("db_host", "localhost");
DEFINE("db_name", "database");
DEFINE("db_username", "username");
DEFINE("db_password", "password");

DEFINE("reset_url", "https://some.domain/reset"); // where you want to redirect users to reset their LDAP password
DEFINE("support_email", "help@some.domain");

# LDAP OPTIONS
define("LDAP_ENABLE", true);
define("LDAP_SERVER", "server");
define("LDAP_PORT", 389);
define("LDAP_STARTTLS", true);
define("LDAP_BIND_DN", "CN=someaccount,DC=some,DC=domain");
define("LDAP_BASE_DN", "DC=some,DC=domain");
define("LDAP_BIND_PASSWORD", "password");
define("LDAP_ALLOWED_DN", 'OU=Users,DC=some,DC=domain');
define("LDAP_ACCOUNT_SUFFIX", '@some.domain');
?>
