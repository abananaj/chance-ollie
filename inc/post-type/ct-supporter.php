<?php

/**
 * Register Supporter Post Types
 */

function ct_register_supporter()
{
	register_post_type('ct-supporter', array(
		'labels' => array(
			'name'          => 'Supporters',
			'singular_name' => 'Supporter',
			'menu_name'     => 'Supporters',
			'all_items'     => 'All Supporters',
			'edit_item'     => 'Edit Supporter',
			'view_item'     => 'View Supporter',
			'view_items'    => 'View Supporters',
			'add_new_item'  => 'Add New Supporter',
			'add_new'       => 'Add New Supporter',
			'new_item'      => 'New Supporter',
		),
		'description'     => 'Supporters custom post type',
		'public'          => true,
		'show_in_rest'    => true,
		'menu_position'   => 26,
		'menu_icon'       => 'dashicons-heart',
		'supports'        => array('title', 'editor', 'excerpt', 'thumbnail'),
		'taxonomies'      => array('supporter-level'),
		'has_archive'     => 'chance-supporters',
		'rewrite'         => false,
		'delete_with_user' => false,
		'hierarchical'    => false,
	));
}
