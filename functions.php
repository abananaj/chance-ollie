<?php
/**
 * Chance Theme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package chance
 */

// add_action( 'wp_enqueue_scripts', 'ollie_parent_theme_enqueue_styles' );

/**
 * Enqueue scripts and styles.
 */
// function ollie_parent_theme_enqueue_styles() {
// 	wp_enqueue_style( 'ollie-style', get_template_directory_uri() . '/style.css' );
// 	wp_enqueue_style( 'chance-style',
// 		get_stylesheet_directory_uri() . '/style.css',
// 		[ 'ollie-style' ]
// 	);
// }

// Allow SVG uploads for administrators
add_filter('upload_mimes', function($mimes) {
    if (current_user_can('administrator')) {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
    }
    return $mimes;
});
