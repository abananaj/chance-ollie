<?php

/**
 *
 * UTILITIES
 *
 * 1. Archived Status
 * 2. Categories & Tags on pages 
 * 3. Duplicate Post/Page
 * 4. Featured Image Default
 * 5. Relative URLs
 * 6. SVG Upload Support
 * 
 */

$utils_dir = get_stylesheet_directory() . '/inc/utils/';

require_once $utils_dir . 'archived-status.php';
add_action('init', 'custom_status_archived');

require_once $utils_dir . 'cat-tag-support.php';
add_action('init', 'ct_tag_support_pages');

// require_once $utils_dir . 'duplicate-post.php';
// add_filter('post_row_actions', 'ct_add_duplicate_action', 10, 2);
// add_filter('page_row_actions', 'ct_add_duplicate_action', 10, 2);
// add_action('admin_init', 'ct_handle_duplicate_post_action');
// add_action('save_post', 'ct_duplicate_post');

require_once $utils_dir . 'feat-img-default.php';
add_action('save_post', 'ct_default_featured_image');

require_once $utils_dir . 'relative-urls.php';
add_action('init', 'ct_enable_root_relative_urls');

require_once $utils_dir . 'svg-uploads.php';
add_filter('upload_mimes', 'ct_svg_allowed_mimes');
add_filter('wp_check_filetype_and_ext', 'ct_svg_check_filetype', 10, 4);

