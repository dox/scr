<?php
$connectionsConfig = [
	'id' => 'connections-day-21',
	'title' => 'Tutorial Room Tangles',
	'subtitle' => 'Today’s sets lean into conversation, critique, and Oxford study culture.',
	'footer' => 'You may hear some of these words over coffee after class.',
	'intro_message' => 'One group sounds exactly like feedback on an essay.',
	'groups' => [
		[
			'name' => 'Feedback words',
			'color' => 'var(--cg-yellow)',
			'words' => ['AMBITIOUS', 'CLEAR', 'PATCHY', 'PERSUASIVE'],
		],
		[
			'name' => 'Tutorial supplies',
			'color' => 'var(--cg-green)',
			'words' => ['ANNOTATIONS', 'DRAFT', 'PEN', 'PRINTOUT'],
		],
		[
			'name' => 'Things in a college dining hall',
			'color' => 'var(--cg-blue)',
			'words' => ['CUTLERY', 'PORTRAIT', 'TABLECLOTH', 'TRAY'],
		],
		[
			'name' => 'Words for explaining',
			'color' => 'var(--cg-purple)',
			'words' => ['DEFINE', 'DEMONSTRATE', 'ILLUSTRATE', 'STATE'],
		],
	],
];

require __DIR__ . '/../_connections.php';
