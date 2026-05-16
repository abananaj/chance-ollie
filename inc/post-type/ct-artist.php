<?php

/**
 * Register Artist Post Types
 */

function ct_register_artist()
{
	register_post_type('ct-artist', array(
		'labels' => array(
			'name'          => 'Artists',
			'singular_name' => 'Artist',
			'menu_name'     => 'Artists',
			'all_items'     => 'All Artists',
			'edit_item'     => 'Edit Artist',
			'view_item'     => 'View Artist',
			'view_items'    => 'View Artists',
			'add_new_item'  => 'Add New Artist',
			'add_new'       => 'Add New',
			'new_item'      => 'New Artist',
		),
		'description'     => 'Artists and Chance Staff',
		'public'          => true,
		'show_in_rest'    => true,
		'menu_position'   => 20,
		'menu_icon'       => 'dashicons-groups',
		'supports'        => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
		'taxonomies'      => array('post_tag'),
		'rewrite'         => false,
		'delete_with_user' => false,
		'hierarchical'    => false,
	));
}
