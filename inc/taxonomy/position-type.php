<?php

function ct_register_position_type()
{
    register_taxonomy(
        'position-type',
        array('position'),
        array(
            'labels' => array(
                'name'                       => 'Position Types',
                'singular_name'              => 'Position Type',
                'all_items'                  => __('All Position Types'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Position Type'),
                'update_item'                => __('Update Position Type'),
                'add_new_item'               => __('Add New Position Type'),
                'new_item_name'              => __('New Position Type Name'),
                'separate_items_with_commas' => __('Enter position type name'),
                'add_or_remove_items'        => __('Add or remove position type from series'),
                'choose_from_most_used'      => __('Choose from the most used position types'),
                'not_found'                  => __('No position types found.'),
                'menu_name'                  => __('Position Types'),
            ),
            'rewrite'           => false,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'hierarchical'      => false,
        )
    );
}
