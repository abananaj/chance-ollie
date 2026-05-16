<?php

/**
 * Chance Theme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 */
add_theme_support('editor-styles');
add_theme_support('wp-block-styles');
add_theme_support('align-wide');
add_theme_support('responsive-embeds');
add_theme_support('custom-spacing');

$inc_dir = get_stylesheet_directory() . '/inc';

// MODELS
require_once $inc_dir . '/models/index.php';

// TAXONOMIES
require_once $inc_dir . '/taxonomy/index.php';
add_action('init', 'ct_register_taxonomies', 0);

// POST TYPES
require_once $inc_dir . '/post-type/index.php';
add_action('init', 'ct_register_post_types');

// UTILS
require_once $inc_dir . '/utils/index.php';

// BLOCK STYLES
require_once $inc_dir . '/block-styles.php';

// META DATA
require_once $inc_dir . '/metadata/index.php';

// ENQUEUE STYLES & SCRIPTS (frontend)
function chance_enqueue_styles()
{
  wp_enqueue_style('chance-styles', get_stylesheet_directory_uri() . '/dist/main.css');
}
add_action('wp_enqueue_scripts', 'chance_enqueue_styles');

function chance_enqueue_scripts()
{
  // wp_enqueue_script('gsap', get_stylesheet_directory_uri() . '/dist/gsap.min.js', array(), '3.12.2', true);
  wp_enqueue_script(
    'main',
    get_stylesheet_directory_uri() . '/dist/main.js',
    //  array('gsap'),
    '1.0',
    true
  );

  // wp_localize_script('main', 'chanceNavColors', array(
  //   get_option('options_onstage_color'),
  //   get_option('options_join_color'),
  //   get_option('options_donate_color'),
  //   get_option('options_education_color'),
  //   get_option('options_backstage_color'),
  // ));
  wp_enqueue_script('feedbucket', get_stylesheet_directory_uri() . '/dist/feedbucket.js', array(), '1.0', false);
}
add_action('wp_enqueue_scripts', 'chance_enqueue_scripts');

// EDITOR STYLES
// add_editor_style injects the stylesheet into the iframed editor canvas
// wrapped in .editor-styles-wrapper, keeping parity with the frontend.
function chance_editor_styles()
{
  add_editor_style('dist/main.css');
}
add_action('after_setup_theme', 'chance_editor_styles');

require_once $inc_dir . '/patterns.php';
add_action('init', 'chance_register_block_patterns', 11);
