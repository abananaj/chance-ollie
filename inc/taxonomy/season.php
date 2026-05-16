<?php

/**
 * Register season taxonomy.
 * SEASONS ( for productions, events, posts, pages )
 */


function ct_register_season()
{
    register_taxonomy(
        'season',
        array('post', 'page', 'ct-production', 'ct-event', 'ct-credit'),
        array(
            'labels' => array(
                'name'                       => 'Seasons',
                'singular_name'              => 'Season',
                'all_items'                  => __('All Seasons'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Season'),
                'update_item'                => __('Update Season'),
                'add_new_item'               => __('Add New Season'),
                'new_item_name'              => __('New Season Name'),
                'separate_items_with_commas' => __('Enter season name'),
                'add_or_remove_items'        => __('Add or remove production from season'),
                'choose_from_most_used'      => __('Choose from the most used seasons'),
                'not_found'                  => __('No seasons found.'),
                'menu_name'                  => __('Seasons'),
            ),
            'hierarchical'      => false,
            'public'            => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'show_admin_column' => true,
        )
    );
}
