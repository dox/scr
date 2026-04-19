<?php
$connectionsConfig = [
	'id' => 'connections-day-9',
	'title' => 'Matriculation Mix',
	'subtitle' => 'A day of gowns, forms, and formal university language.',
	'footer' => 'Very official, slightly ceremonial, still a puzzle.',
	'intro_message' => 'One set belongs to ceremony more than study.',
	'groups' => [
		[
			'name' => 'Ceremonial clothing',
			'color' => 'var(--cg-yellow)',
			'words' => ['CAP', 'GOWN', 'HOOD', 'SUBFUSC'],
		],
		[
			'name' => 'Administrative words',
			'color' => 'var(--cg-green)',
			'words' => ['REGISTER', 'SIGN', 'STAMP', 'VERIFY'],
		],
		[
			'name' => 'Assessment words',
			'color' => 'var(--cg-blue)',
			'words' => ['EXAMINER', 'MARK', 'PAPER', 'SCRIPT'],
		],
		[
			'name' => 'Found in a chapel',
			'color' => 'var(--cg-purple)',
			'words' => ['ALTAR', 'CANDLE', 'ORGAN', 'PEW'],
		],
	],
];

require __DIR__ . '/../_connections.php';
