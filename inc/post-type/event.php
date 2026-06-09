<?php

/**
 * Register Event Post Types
 */

function ct_register_event()
{
	register_post_type('event', array(
		'labels' => array(
			'name'                => 'Events',
			'singular_name'       => 'Event',
			'menu_name'           => 'Events',
			'all_items'           => 'All Events',
			'edit_item'           => 'Edit Event',
			'view_item'           => 'View Event',
			'view_items'          => 'View Events',
			'add_new_item'        => 'Add New Event',
			'add_new'             => 'Add New',
			'new_item'            => 'New Event',
			'parent_item_colon'   => 'Parent Event:',
			'search_items'        => 'Search Events',
			'not_found'           => 'No events found',
			'not_found_in_trash'  => 'No events found in Trash',
		),
		'description'     => 'Events custom post type',
		'public'          => true,
		'show_in_rest'    => true,
		'menu_position'   => 21,
		'menu_icon'       => 'dashicons-calendar',
		'supports'        => array('title', 'editor', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields'),
		'taxonomies'      => array('event-type', 'season', 'category', 'post_tag'),
		'has_archive'     => 'chance-events',
		'hierarchical'    => false,
		'rewrite'         => false,
		'delete_with_user' => false,
	));
}
