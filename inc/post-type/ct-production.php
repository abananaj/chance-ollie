<?php

/**
 * Register Production Post Types
 */

function ct_register_production()
{
	register_post_type('ct-production', array(
		'labels' => array(
			'name'          => 'Productions',
			'singular_name' => 'Production',
			'menu_name'     => 'Productions',
			'all_items'     => 'All Productions',
			'edit_item'     => 'Edit Production',
			'view_item'     => 'View Production',
			'view_items'    => 'View Productions',
			'add_new_item'  => 'Add New Production',
			'add_new'       => 'Add New Production',
			'new_item'      => 'New Production',
		),
		'description'     => 'Productions custom post type',
		'public'          => true,
		'show_in_rest'    => true,
		'menu_position'   => 23,
		'menu_icon'       => 'dashicons-book',
		'supports'        => array('title', 'comments', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'post-formats'),
		'taxonomies'      => array('series', 'season', 'post_tag'),
		'has_archive'     => 'chance-productions',
		'rewrite'         => false,
		'delete_with_user' => false,
		'hierarchical'    => false,
	));
}
