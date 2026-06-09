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
        array('supporter'),
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
 * Add "Related Page" column to supporter-level taxonomy list
 */
function ct_add_supporter_level_columns($columns)
{
    $new_columns = [];
    $i = 0;
    
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        $i++;
        
        if ($i === 2) {
            $new_columns['term_related_page'] = __('Related Page', 'chance-ollie');
        }
    }
    
    return $new_columns;
}
add_filter('manage_edit-supporter-level_columns', 'ct_add_supporter_level_columns');

/**
 * Display term meta value in the "Related Page" column for supporter-level
 */
function ct_display_supporter_level_meta_column($content, $column_name, $term_id)
{
    if ($column_name !== 'term_related_page') {
        return $content;
    }

    $term_related_page = get_term_meta($term_id, 'term_related_page', true);
    
    if (empty($term_related_page)) {
        return '—';
    }

    $page_ids = is_array($term_related_page) ? $term_related_page : [$term_related_page];
    $links = [];

    foreach ($page_ids as $page) {
        $post_id = is_object($page) ? $page->ID : (int) $page;
        
        if (get_post($post_id)) {
            $edit_url = get_edit_post_link($post_id);
            $post_title = get_the_title($post_id);
            
            if ($edit_url) {
                $links[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url($edit_url),
                    esc_html($post_title ?: "Post #$post_id")
                );
            }
        }
    }

    return !empty($links) ? implode(', ', $links) : '—';
}
add_filter('manage_supporter-level_custom_column', 'ct_display_supporter_level_meta_column', 10, 3);

/**
 * Add "Related Page" field to Quick Edit form for supporter-level
 */
function ct_supporter_level_quick_edit_custom_box($column_name, $screen, $taxonomy)
{
    if ($column_name !== 'term_related_page' || $taxonomy !== 'supporter-level') {
        return;
    }

    $pages = get_posts([
        'post_type'      => 'page',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);
    
    $page_hierarchy = ct_build_page_hierarchy($pages);
    ?>
    <fieldset>
        <div class="inline-edit-group wp-clearfix">
            <label>
                <span class="title"><?php esc_html_e('Related Page', 'chance-ollie'); ?></span>
                <select name="term_related_page_id" class="term-related-page-select ptitle">
                    <option value=""><?php esc_html_e('— None —', 'chance-ollie'); ?></option>
                    <?php ct_display_page_hierarchy($page_hierarchy); ?>
                </select>
            </label>
        </div>
    </fieldset>
    <?php
}
add_action('quick_edit_custom_box', 'ct_supporter_level_quick_edit_custom_box', 10, 3);

/**
 * Populate Quick Edit form with current meta value for supporter-level
 */
function ct_supporter_level_edit_form_load()
{
    if (!isset($_GET['tag_ID'])) {
        return;
    }

    $term_id = (int) $_GET['tag_ID'];
    $term_related_page = get_term_meta($term_id, 'term_related_page', true);
    
    if (empty($term_related_page)) {
        return;
    }

    $post_id = is_array($term_related_page) ? reset($term_related_page) : $term_related_page;
    $post_id = is_object($post_id) ? $post_id->ID : (int) $post_id;
    
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var select = document.querySelector(".term-related-page-select");
            if (select) {
                select.value = ' . (int) $post_id . ';
            }
        });
    </script>';
}
add_action('admin_footer', 'ct_supporter_level_edit_form_load');

/**
 * Save term_related_page meta when supporter-level term is updated
 */
function ct_supporter_level_save_term_meta($term_id, $tt_id, $taxonomy)
{
    if ($taxonomy !== 'supporter-level' || !isset($_POST['term_related_page_id'])) {
        return;
    }

    $post_id = (int) $_POST['term_related_page_id'];
    
    if ($post_id > 0) {
        update_term_meta($term_id, 'term_related_page', $post_id);
    } else {
        delete_term_meta($term_id, 'term_related_page');
    }
}
add_action('edit_supporter-level', 'ct_supporter_level_save_term_meta', 10, 3);

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
