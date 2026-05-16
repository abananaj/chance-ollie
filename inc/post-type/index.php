<?php

/**
 * Register Custom Post Types
 */

$post_type_dir = get_stylesheet_directory() . '/inc/post-type/';

require_once $post_type_dir . 'ct-artist.php';
require_once $post_type_dir . 'ct-class.php';
require_once $post_type_dir . 'ct-credit.php';
require_once $post_type_dir . 'ct-event.php';
require_once $post_type_dir . 'ct-production.php';
require_once $post_type_dir . 'ct-supporter.php';
require_once $post_type_dir . 'ct-venue.php';

function ct_register_post_types()
{
	ct_register_artist();
	ct_register_class();
	ct_register_credit();
	ct_register_event();
	ct_register_production();
	ct_register_supporter();
	ct_register_venue();
}

//  Custom admin views on post type all posts pages.
require_once $post_type_dir . 'admin-views/ct-artist-all.php';
require_once $post_type_dir . 'admin-views/ct-class-all.php';
require_once $post_type_dir . 'admin-views/ct-event-all.php';
require_once $post_type_dir . 'admin-views/ct-production-all.php';
require_once $post_type_dir . 'admin-views/ct-supporter-all.php';