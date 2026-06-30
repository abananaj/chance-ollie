<?php

/**
 * METADATA
 *
 * - Block Bindings (links ACF fields to block editor data sources)
 * - Fallback Image (helper functions for images with fallbacks)
 * - Filter By (extends core/query with meta filter inspector panel)
 * - Order By (extends core/query with meta sort inspector panel)
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