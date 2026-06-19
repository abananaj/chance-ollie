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
file_exists(get_stylesheet_directory() . '/inc/metadata/fallback-img.php') && require_once get_stylesheet_directory() . '/inc/metadata/fallback-img.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/filter-by.php') && require_once get_stylesheet_directory() . '/inc/metadata/filter-by.php';
file_exists(get_stylesheet_directory() . '/inc/metadata/order-by.php') && require_once get_stylesheet_directory() . '/inc/metadata/order-by.php';

// Load ACF field group definitions
add_filter('acf/settings/save_json', function() {
    return get_stylesheet_directory() . '/inc/metadata/acf/json';
});

add_filter('acf/settings/load_json', function($paths) {
    $paths[] = get_stylesheet_directory() . '/inc/metadata/acf/json';
    return $paths;
});