<?php

/**
 * Register supporter taxonomy.
 * SUPPORTER LEVELS ✨ ( for supporters )
 *
 */
function ct_register_supporter_level()
{
    register_taxonomy(
        'supporter-level',
        array('ct-supporter'),
        array(
            'labels'            => array(
                'name'                       => 'Support Levels',
                'singular_name'              => 'Support Level',
                'all_items'                  => __('All Support Levels'),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __('Edit Support Level'),
                'update_item'                => __('Update Support Level'),
                'add_new_item'               => __('Add New Support Level'),
                'menu_name'                  => __('Support Levels'),
                'new_item_name'              => __('New Support Level'),
                'separate_items_with_commas' => __('Enter new support level'),
                'add_or_remove_items'        => __('Add or remove support levels'),
                'choose_from_most_used'      => __('Choose from the most used support levels'),
                'not_found'                  => __('No support levels found.'),
            ),
            'hierarchical'      => false,
            'show_in_rest'      => true,
            'public'            => true,
            'show_tagcloud'     => false,
            'query_var'         => true,
            'show_admin_column' => true,
        )
    );
}

/**
 * Order supporter-level terms by menu_order ascending by default.
 * 
 * Uses the get_terms_orderby filter to modify the SQL directly,
 * allowing custom ordering without filtering out terms without meta.
 * 
 * @link https://wordpress.stackexchange.com/questions/92213/order-terms-by-term-order
 * 
 * @param string $orderby    The ORDER BY clause.
 * @param array  $query_vars The get_terms() query variables.
 * @param array  $taxonomies The list of taxonomies being queried.
 * @return string Modified ORDER BY clause.
 */
// function ct_order_supporter_levels_orderby($orderby, $query_vars, $taxonomies)
// {
//     // Only apply to supporter-level taxonomy if not explicitly ordered otherwise
//     if (
//         is_array($taxonomies) && in_array('supporter-level', $taxonomies, true)
//         && (! isset($query_vars['orderby']) || 'name' === $query_vars['orderby'])
//     ) {
//         global $wpdb;
//         // Order by term meta menu_order, defaulting to 0 if not set
//         $orderby = "CAST(COALESCE(tm.meta_value, 0) AS SIGNED) ASC";
//     }
//     return $orderby;
// }
// add_filter('get_terms_orderby', 'ct_order_supporter_levels_orderby', 10, 3);

/**
 * Join termmeta table for supporter-level ordering.
 * 
 * Adds the termmeta table to the query so we can order by menu_order values.
 * 
 * @param string $clause The SQL JOIN clause.
 * @param array  $query_vars The get_terms() query variables.
 * @param array  $taxonomies The list of taxonomies being queried.
 * @return string Modified JOIN clause.
 */
// function ct_order_supporter_levels_join($clause, $query_vars, $taxonomies)
// {
//     // Only apply to supporter-level taxonomy if not explicitly ordered otherwise
//     if (
//         is_array($taxonomies) && in_array('supporter-level', $taxonomies, true)
//         && (! isset($query_vars['orderby']) || 'name' === $query_vars['orderby'])
//     ) {
//         global $wpdb;
//         // Add LEFT JOIN to termmeta table for menu_order values
//         if (strpos($clause, 'LEFT JOIN ' . $wpdb->termmeta . ' AS tm') === false) {
//             $clause .= " LEFT JOIN {$wpdb->termmeta} AS tm ON t.term_id = tm.term_id AND tm.meta_key = 'menu_order'";
//         }
//     }
//     return $clause;
// }
// add_filter('get_terms_join', 'ct_order_supporter_levels_join', 10, 3);
