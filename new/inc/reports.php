<?php
// All available reports
$reports = [
	// Bookings
	'bookings' => [
		'title' => 'Meal bookings',
		'description' => 'All meal bookings for all members within a specified period',
		'format' => 'csv',
		'file' => 'bookings.php',
		'requiresDateRange' => true,
	],
	'bookings_late' => [
		'title' => 'Late meal bookings by member',
		'description' => 'All late meal bookings for all members within a specified period',
		'format' => 'html',
		'file' => 'bookings_late.php',
		'requiresDateRange' => true,
	],
	'bookings_orphaned' => [
		'title' => 'Orphaned meal bookings',
		'description' => 'Meal bookings that do not match a current member',
		'format' => 'html',
		'file' => 'bookings_orphaned.php',
		'requiresDateRange' => false,
	],
	
	// Meals
	'meal_bookings' => [
		'title' => 'Meal bookings',
		'description' => 'All bookings for specific meal',
		'format' => 'csv',
		'file' => 'meal_bookings.php',
		'requiresDateRange' => false,
	],
	
	// Members
	'members' => [
		'title' => 'Members',
		'description' => 'All members',
		'format' => 'csv',
		'file' => 'members.php',
		'requiresDateRange' => false,
	],
	'member_bookings' => [
		'title' => 'Member bookings',
		'description' => 'All bookings for specific member',
		'format' => 'csv',
		'file' => 'member_bookings.php',
		'requiresDateRange' => false,
		'hidden' => true,
	],

	// Wine
	'wine_stock' => [
		'title' => 'Wine (current stock)',
		'description' => 'All current wines (excluding closed) and their details',
		'format' => 'csv',
		'file' => 'wine_stock.php',
		'requiresDateRange' => false,
	],
	'wine_complete' => [
		'title' => 'Wine (complete)',
		'description' => 'All wines (including closed) and their details',
		'format' => 'csv',
		'file' => 'wine_complete.php',
		'requiresDateRange' => false,
	],
	'wine_suppliers' => [
		'title' => 'Wine suppliers',
		'description' => 'All current wines (excluding closed) listed by supplier',
		'format' => 'csv',
		'file' => 'wine_suppliers.php',
		'requiresDateRange' => false,
	],
	'wine_transactions' => [
		'title' => 'Wine transactions',
		'description' => 'Wine transactions within a specified period',
		'format' => 'csv',
		'file' => 'wine_transactions.php',
		'requiresDateRange' => true,
	],
	'wine_valuation' => [
		'title' => 'Wine valuation',
		'description' => 'A complete stock valuation of all current wines',
		'format' => 'html',
		'file' => 'wine_valuation.php',
		'requiresDateRange' => false,
	],
];
