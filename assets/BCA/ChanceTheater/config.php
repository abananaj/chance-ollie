<?php
// @codingStandardsIgnoreFile

/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: b34a211639d27f4162950013f3e26d94542095a8 $
 * @link       http://chancetheater.com/
 */
add_theme_support('root-relative-urls');
// Enable relative URLs
add_theme_support('rewrites');
// Enable URL rewrites
add_theme_support('bootstrap-top-navbar');
// Enable Bootstrap's top navbar
add_theme_support('bootstrap-gallery');
// Enable Bootstrap's thumbnails component on [gallery]
add_theme_support('nice-search');
// Enable /?s= to /search/ redirect
add_theme_support('jquery-cdn');
// Enable to load jQuery from the Google CDN
/*
 * Configuration values
 */
define('FB_APP_ID', '272505216207671');
define('GOOGLE_ANALYTICS_ID', 'UA-44649201-1');
define('GOOGLE_API_BROWSER_KEY', 'AIzaSyBqi0XOivvv79cxHjYsMybnT0QTrKydFcU');
define('POST_EXCERPT_LENGTH', 40);

/**
 * .main classes
 */
function roots_main_class()
{
    if (roots_display_sidebar()) {
        // Classes on pages with the sidebar
        $class = 'col-md-9';
    } else {
        // Classes on full width pages
        $class = 'col-md-12';
    }

    return $class;
}

/**
 * .sidebar classes
 */
function roots_sidebar_class()
{
    return 'col-md-3';
}

/**
 * Define which pages shouldn't have the sidebar
 *
 * See lib/sidebar.php for more details
 */
function roots_display_sidebar()
{
    $sidebar_config = new Roots_Sidebar(
        /*
        * Conditional tag checks (http://codex.wordpress.org/Conditional_Tags)
        * Any of these conditional tags that return true won't show the sidebar
        *
        * To use a function that accepts arguments, use the following format:
        *
        * array('function_name', array('arg1', 'arg2'))
        *
        * The second element must be an array even if there's only 1 argument.
        */
        [
        'is_404',
        'is_front_page',
        ['is_post_type_archive', ['ct-event']]
        ],
        /*
        * Page template checks (via is_page_template())
        * Any of these page templates that return true won't show the sidebar
        */
        [
        'pagefullwidth.php'
        ]
    );

    return apply_filters('roots_display_sidebar', $sidebar_config->display);
}

/*
 * $content_width is a global variable used by WordPress for max image upload sizes
 * and media embeds (in pixels).
 *
 * Example: If the content area is 640px wide, set $content_width = 620; so images and videos will not overflow.
 * Default: 1140px is the default Bootstrap container width.
 */
if (!isset($content_width)) {
    $content_width = 1140;
}

/*
 * Define helper constants
 */
$get_theme_name = explode('/themes/', get_template_directory());

// define('THEME_NAME',            next($get_theme_name));
// define('THEME_PATH',            RELATIVE_CONTENT_PATH . '/themes/' . THEME_NAME);
