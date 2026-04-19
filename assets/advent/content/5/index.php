<?php
$connectionsConfig = [
	'id' => 'connections-day-5',
	'title' => 'St Edmund Hall Starter',
	'subtitle' => 'Sort the words into four university-themed quartets.',
	'footer' => 'Teddy Hall gets its turn in the spotlight.',
	'intro_message' => 'Some groups are Hall-specific, some are classic student life.',
	'groups' => [
		[
			'name' => 'Student statuses',
			'color' => 'var(--cg-yellow)',
			'words' => ['ALUMNUS', 'FINALIST', 'FRESHER', 'SCHOLAR'],
		],
		[
			'name' => 'Things on a timetable',
			'color' => 'var(--cg-green)',
			'words' => ['DEADLINE', 'LECTURE', 'MEETING', 'REVISION'],
		],
		[
			'name' => 'Likely in a quad',
			'color' => 'var(--cg-blue)',
			'words' => ['ARCH', 'BENCH', 'GRASS', 'LANTERN'],
		],
		[
			'name' => 'Pieces of academic writing',
			'color' => 'var(--cg-purple)',
			'words' => ['ABSTRACT', 'BIBLIOGRAPHY', 'FOOTNOTE', 'THESIS'],
		],
	],
];

require __DIR__ . '/../_connections.php';
