<?php

/**
 * Register Class Post Types
 */

function ct_register_position()
{
	register_post_type('position', array(
		'labels' => array(
			'name'          => 'Positions',
			'singular_name' => 'Position',
			'menu_name'     => 'Positions',
			'all_items'     => 'All Positions',
			'edit_item'     => 'Edit Position',
			'view_item'     => 'View Position',
			'view_items'    => 'View Positions',
			'add_new_item'  => 'Add New Position',
			'add_new'       => 'Add New Position',
			'new_item'      => 'New Position',
		),
		'description'     => 'Positions categorized by Job, Volunteer, and Internship',
		'public'          => true,
		'show_in_menu'       => 'edit.php?post_type=artist',
		'show_in_rest'    => true,
		'menu_position'   => 27,
		'menu_icon'       => 'dashicons-welcome-learn-more',
		'supports'        => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
		'taxonomies'      => array('position-type'),
		'has_archive'     => 'chance-positions',
		'rewrite'         => false,
		'delete_with_user' => false,
		'hierarchical'    => false,
	));
}
