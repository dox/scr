<?php
$connectionsConfig = [
	'id' => 'connections-day-19',
	'title' => 'Oxford by Bicycle',
	'subtitle' => 'A city of colleges and bikes becomes a board of connections.',
	'footer' => 'Pedal gently toward the right sets.',
	'intro_message' => 'One group definitely belongs in Oxford street traffic.',
	'groups' => [
		[
			'name' => 'Bicycle words',
			'color' => 'var(--cg-yellow)',
			'words' => ['BRAKE', 'CHAIN', 'PEDAL', 'SADDLE'],
		],
		[
			'name' => 'Study locations',
			'color' => 'var(--cg-green)',
			'words' => ['ARCHIVE', 'CAFE', 'LIBRARY', 'SEMINARROOM'],
		],
		[
			'name' => 'Words in a conclusion',
			'color' => 'var(--cg-blue)',
			'words' => ['FINALLY', 'OVERALL', 'THEREFORE', 'THUS'],
		],
		[
			'name' => 'Things that ring',
			'color' => 'var(--cg-purple)',
			'words' => ['ALARM', 'CLOCK', 'PHONE', 'TOWERBELL'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
