<?php

/**
 * 
 * ARCHIVED STATUS
 * 
 * 
 */


function custom_status_archived()
{

    register_post_status(
        'featured',
        array(
            'label' => 'Featured',
            'label_count' => _n_noop('Featured <span class="count">(%s)</span>', 'Featured <span class="count">(%s)</span>'),
            'public' => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
        )
    );
}
add_action('init', 'custom_status_archived');
