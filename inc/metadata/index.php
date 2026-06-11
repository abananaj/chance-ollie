<?php

/**
 * 
 * MODELS
 * 
 * 1. Artists
 * 2. Events
 * 3. Productions
 * 4. Supporters
 * 5. Venues
 * 
 * - Calendar (for events and productions)
 * - Cleanup (for deleting old events and productions)
 * - Inflect (for pluralizing words based on quantity)
 * - Shortcodes (for rendering shortcodes in templates)
 * 
 */

// ======== 

file_exists(get_stylesheet_directory() . '/inc/metadata/block-bindings.php') && require_once get_stylesheet_directory() . '/inc/metadata/block-bindings.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/filter-by-meta.php') && require_once get_stylesheet_directory() . '/inc/metadata/filter-by-meta.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/fallback-img.php') && require_once get_stylesheet_directory() . '/inc/metadata/fallback-img.php';

file_exists(get_stylesheet_directory() . '/inc/metadata/sort-by-meta.php') && require_once get_stylesheet_directory() . '/inc/metadata/sort-by-meta.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/filter-by-url-params.php') && require_once get_stylesheet_directory() . '/inc/metadata/filter-by-url-params.php';

// Load ACF field group definitions
$acf_dir = get_stylesheet_directory() . '/inc/metadata/acf/php/';
$acf_files = array(
	'group_production_details.php',
	'group_event_details.php',
	'group_artist_profile.php',
	'group_supporter_profile.php',
	'group_venue_details.php',
	'group_69c1dd787d5ba.php',
	'group_69c3515383d9f.php',
	'group_69d8bd5aea722.php',
	'group_69d9a4e3b3e3d.php',
	'group_69d9cc6036f9c.php',
	'group_69d9ee6540cad.php',
	'group_69dadd003f7ef.php',
	'group_69e1b683517f7.php',
	'group_69ec80e948a54.php',
);

foreach ($acf_files as $file) {
	$file_path = $acf_dir . $file;
	if (file_exists($file_path)) {
		require_once $file_path;
	}
}
