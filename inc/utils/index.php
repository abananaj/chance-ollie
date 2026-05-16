<?php

/**
 *
 * UTILITIES
 *
 * 1. Archived Status
 * 2. Categories & Tags on pages
 * 3. Featured Image Default
 * 4. Relative URLs
 * 5. SVG Upload Support
 */

$utils_dir = get_stylesheet_directory() . '/inc/utils/';

require_once $utils_dir . 'archived-status.php';
require_once $utils_dir . 'cat-tag-support.php';
// require_once $utils_dir . 'duplicate-post.php';
require_once $utils_dir . 'feat-img-default.php';
require_once $utils_dir . 'relative-urls.php';
require_once $utils_dir . 'svg-uploads.php';
