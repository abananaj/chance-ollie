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
