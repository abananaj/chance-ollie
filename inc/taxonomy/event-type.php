<?php

/**
 * Register event type taxonomy.
 * EVENT TYPE ✨ ( for events )
 *
 */

function ct_register_event_type()
{
    register_taxonomy(
        'event-type',
        array('event'),
        array(
            'labels' => array(
                'name'                       => 'Event Types',
                'singular_name'              => 'Event Type',
                'all_items'                  => __('All Event Types'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Event Type'),
                'update_item'                => __('Update Event Type'),
                'add_new_item'               => __('Add New Event Type'),
                'new_item_name'              => __('New Event Type Name'),
                'separate_items_with_commas' => __('Enter event type name'),
                'add_or_remove_items'        => __('Add or remove event type from series'),
                'choose_from_most_used'      => __('Choose from the most used event types'),
                'not_found'                  => __('No event types found.'),
                'menu_name'                  => __('Event Types'),
            ),
            'rewrite'           => false,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'hierarchical'      => false,
        )
    );
}
