<?php

/**
 * Register series taxonomy.
 * SERIES ✨ ( for productions, events, posts, pages )
 */

function ct_register_series()
{
    register_taxonomy(
        'series',
        array('post', 'page', 'ct-production'),
        array(
            'labels' => array(
                'name'                       => 'Series',
                'singular_name'              => 'Series',
                'all_items'                  => __('All Series'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Series'),
                'update_item'                => __('Update Series'),
                'add_new_item'               => __('Add New Series'),
                'new_item_name'              => __('New Series Name'),
                'separate_items_with_commas' => __('Enter series name'),
                'add_or_remove_items'        => __('Add or remove production from series'),
                'choose_from_most_used'      => __('Choose from the most used series'),
                'not_found'                  => __('No series found.'),
                'menu_name'                  => __('Series'),
            ),
            'hierarchical'      => false,
            'public'            => true,
            'show_in_rest'      => true,
            'show_tagcloud'     => false,
            'query_var'         => true,
            'rewrite'           => false,
            'show_admin_column' => true,
        )
    );
}
