<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: e20ff1df7d6809f8b98bfa82388f5f0411e1b400 $
 * @link       http://chancetheater.com/
 */

add_action('after_setup_theme', function () {
    // Disable WordPress Toolbar.
    show_admin_bar(false);

    // Make theme available for translation.
    load_theme_textdomain('roots', get_template_directory().'/lang');

    // Add post thumbnails.
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(200, 200, true);
    add_image_size('attachment', 600, 600, false);
    add_image_size('mini', 150, 150, false);
    add_image_size('small', 250, 250, false);
    add_image_size('masthead', 1000, 250, false);
    add_image_size('square-42', 42, 42, true);
    add_image_size('square-100', 100, 100, true);
    add_image_size('square-125', 125, 125, true);
    add_image_size('square-150', 150, 150, true);
    add_image_size('banner-wide', 878, 175, true);
    add_image_size('postcard-sm', 150, 233, true);
    add_image_size('supporter-logo', 150, 45, false);
    add_image_size('production-season-header', 300, 125, true);

    // Set default attachment link type.
    update_option('image_default_link_type', 'post');

    // Add post formats.
    add_theme_support(
        'post-formats',
        [
            'gallery',
            'link',
            'image',
            'quote',
            'status',
            'video'
        ]
    );

    // Init Classes.
    new \BCA\ChanceTheater\Cleanup;
    new \BCA\ChanceTheater\Shortcodes;
});

add_action('admin_enqueue_scripts', function () {
    // Tell the TinyMCE editor to use a custom stylesheet.
    add_editor_style('/assets/css/editor-style.min.css');

    // Override path to select2 script and jquery-ui styles.
    wp_enqueue_script('select2', '/../app/assets/vendor/select2/select2.js', ['jquery']);
    wp_enqueue_style('select2', '/../app/assets/vendor/select2/select2.css');
    wp_enqueue_style('cmb-jquery-ui', '/../app/assets/vendor/jquery-ui/themes/dark-hive/jquery-ui.min.css');
});

add_action('widgets_init', function () {
    register_widget('\BCA\ChanceTheater\Widgets\EventCalendar');
    register_widget('\BCA\ChanceTheater\Widgets\Productions');
    register_widget('\BCA\ChanceTheater\Widgets\Supporter');
    register_widget('\BCA\ChanceTheater\Widgets\SocialIcons');
});
