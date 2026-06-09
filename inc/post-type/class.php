<?php

/**
 * Register Class Post Types
 */

function ct_register_class()
{
	register_post_type('class', array(
		'labels' => array(
			'name'          => 'Classes',
			'singular_name' => 'Class',
			'menu_name'     => 'Conservatory',
			'all_items'     => 'All Classes',
			'edit_item'     => 'Edit Class',
			'view_item'     => 'View Class',
			'view_items'    => 'View Classes',
			'add_new_item'  => 'Add New Class',
			'add_new'       => 'Add New Class',
			'new_item'      => 'New Class',
		),
		'description'     => 'Conservatory classes categorized by program',
		'public'          => true,
		'show_in_rest'    => true,
		'menu_position'   => 27,
		'menu_icon'       => 'dashicons-welcome-learn-more',
		'supports'        => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
		'taxonomies'      => array('program', 'session'),
		'has_archive'     => 'chance-classes',
		'rewrite'         => false,
		'delete_with_user' => false,
		'hierarchical'    => false,
	));
}
