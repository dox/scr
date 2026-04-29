<?php
$connectionsConfig = [
	'id' => 'connections-day-6',
	'title' => 'Bodleian Brain Teaser',
	'subtitle' => 'Find the four hidden groups among these Oxford-adjacent words.',
	'footer' => 'Quiet in the reading room, noisy in the puzzle brain.',
	'intro_message' => 'Libraries, essays, and campus landmarks all make an appearance.',
	'groups' => [
		[
			'name' => 'Library words',
			'color' => 'var(--cg-yellow)',
			'words' => ['CATALOGUE', 'INDEX', 'SHELF', 'STACK'],
		],
		[
			'name' => 'Essay materials',
			'color' => 'var(--cg-green)',
			'words' => ['DRAFT', 'EVIDENCE', 'QUOTE', 'SOURCE'],
		],
		[
			'name' => 'Ways to move through Oxford',
			'color' => 'var(--cg-blue)',
			'words' => ['CYCLE', 'ROW', 'STROLL', 'WALK'],
		],
		[
			'name' => 'Stone features',
			'color' => 'var(--cg-purple)',
			'words' => ['ARCHWAY', 'CLOISTER', 'TOWER', 'WINDOW'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
