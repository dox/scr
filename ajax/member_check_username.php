<?php
header('Content-Type: application/json');
require_once '../inc/autoload.php';
$user->pageCheck('members');

$ldap = trim($_GET['ldap'] ?? '');

if ($ldap === '') {
	echo json_encode(['valid' => false, 'message' => 'Username is required']);
	exit;
}

// Attempt to fetch a member
$member = Member::fromLDAP($ldap);

if (isset($member->uid)) {
	echo json_encode(['valid' => false, 'message' => 'Username already exists']);
} else {
	echo json_encode(['valid' => true, 'message' => 'Username is available']);
}
