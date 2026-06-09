<?php

/**
 * Register session taxonomy.
 * SESSION ✨ ( for classes )
 *
 */

function ct_register_session()
{
    register_taxonomy(
        'session',
        array('class'),
        array(
            'labels' => array(
                'name'                       => 'Sessions',
                'singular_name'              => 'Session',
                'all_items'                  => __('All Sessions'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Session'),
                'update_item'                => __('Update Session'),
                'add_new_item'               => __('Add New Session'),
                'new_item_name'              => __('New Session Name'),
                'separate_items_with_commas' => __('Enter session name'),
                'add_or_remove_items'        => __('Add or remove session'),
                'choose_from_most_used'      => __('Choose from the most used sessions'),
                'not_found'                  => __('No sessions found.'),
                'menu_name'                  => __('Sessions'),
            ),
            'rewrite'           => false,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'hierarchical'      => false,
        )
    );
}
