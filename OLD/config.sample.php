<?php
DEFINE("debug", false);

DEFINE("site_name", "SCR Meal Booking");

DEFINE("salt", "UNIQUE_SALT");  // keep this private!

DEFINE("db_host", "localhost");
DEFINE("db_name", "database");
DEFINE("db_username", "username");
DEFINE("db_password", "password");

DEFINE("smtp_server", "smtp.some-server.com");
DEFINE("smtp_sender_address", "noreply@some-server.com");
DEFINE("smtp_sender_name", site_name);

DEFINE("reset_url", "https://some.domain/reset"); // where you want to redirect users to reset their LDAP password
DEFINE("support_email", "help@some.domain");

DEFINE("navbar_addon", array(
  'calendar' => array(
    'name'    => 'Some Link',
    'icon'    => '<svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg>',
    'url'     => 'https://www.google.com'
  )
));

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
