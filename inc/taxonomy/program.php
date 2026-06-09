<?php

/**
 * Register event type taxonomy.
 * EVENT TYPE ✨ ( for events )
 *
 */

function ct_register_program()
{
    register_taxonomy(
        'program',
        array('class'),
        array(
            'labels' => array(
                'name'                       => 'Programs',
                'singular_name'              => 'Program',
                'all_items'                  => __('All Programs'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Program'),
                'update_item'                => __('Update Program'),
                'add_new_item'               => __('Add New Program'),
                'new_item_name'              => __('New Program Name'),
                'separate_items_with_commas' => __('Enter program name'),
                'add_or_remove_items'        => __('Add or remove program from series'),
                'choose_from_most_used'      => __('Choose from the most used programs'),
                'not_found'                  => __('No programs found.'),
                'menu_name'                  => __('Programs'),
            ),
            'rewrite'           => false,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'hierarchical'      => false,
        )
    );
}
