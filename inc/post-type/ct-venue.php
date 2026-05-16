<?php

/**
 * Register Venue Post Types
 */

function ct_register_venue()
{
  register_post_type('ct-venue', array(
    'labels' => array(
      'name'          => 'Venues',
      'singular_name' => 'Venue',
      'menu_name'     => 'Venues',
      'all_items'     => 'All Venues',
      'edit_item'     => 'Edit Venue',
      'view_item'     => 'View Venue',
      'view_items'    => 'View Venues',
      'add_new_item'  => 'Add New Venue',
      'add_new'       => 'Add New Venue',
      'new_item'      => 'New Venue',
    ),
    'description'     => 'Venues custom post type',
    'public'          => true,
    'show_in_rest'    => true,
    'menu_position'   => 30,
    'menu_icon'       => 'dashicons-admin-home',
    'supports'        => array('title', 'editor', 'page-attributes', 'thumbnail', 'custom-fields'),
    'taxonomies'      => array('post_tag'),
    'rewrite'         => false,
    'delete_with_user' => false,
    'hierarchical'    => false,
  ));
}
