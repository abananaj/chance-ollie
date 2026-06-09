<?php

/**
 * Register Custom Post Types
 */

$post_type_dir = get_stylesheet_directory() . '/inc/post-type/';

require_once $post_type_dir . 'artist.php';
require_once $post_type_dir . 'class.php';
require_once $post_type_dir . 'credit.php';
require_once $post_type_dir . 'event.php';
require_once $post_type_dir . 'production.php';
require_once $post_type_dir . 'supporter.php';
require_once $post_type_dir . 'venue.php';

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
require_once $post_type_dir . 'admin-views/artist-all.php';
require_once $post_type_dir . 'admin-views/class-all.php';
require_once $post_type_dir . 'admin-views/event-all.php';
require_once $post_type_dir . 'admin-views/production-all.php';
require_once $post_type_dir . 'admin-views/supporter-all.php';
