<?php

/**
 * Register season taxonomy.
 * SEASONS ( for productions, events, posts, pages )
 */


function ct_register_season()
{
    register_taxonomy(
        'season',
        array('post', 'page', 'production', 'event', 'credit'),
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

    // Sort seasons by name descending (most recent year first) at query time
    add_filter('get_terms_args', 'ct_sort_seasons_by_year_args', 10, 2);
}

function ct_sort_seasons_by_year_args($args, $taxonomies)
{
    // Only apply to season taxonomy in admin
    if (!is_admin() || !in_array('season', (array) $taxonomies)) {
        return $args;
    }

    // Set query to sort by name in descending order
    $args['orderby'] = 'name';
    $args['order']   = 'DESC';

    return $args;
}

/**
 * Add "Related Page" column to season taxonomy list
 */
function ct_add_season_columns($columns)
{
    // Convert to array to maintain order
    $new_columns = [];
    $i = 0;
    
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        $i++;
        
        // Insert after the second column
        if ($i === 2) {
            $new_columns['term_related_page'] = __('Related Page', 'chance-ollie');
        }
    }
    
    return $new_columns;
}
add_filter('manage_edit-season_columns', 'ct_add_season_columns');

/**
 * Display term meta value in the "Related Page" column
 */
function ct_display_season_meta_column($content, $column_name, $term_id)
{
    if ($column_name !== 'term_related_page') {
        return $content;
    }

    $term_related_page = get_term_meta($term_id, 'term_related_page', true);
    
    if (empty($term_related_page)) {
        return '—';
    }

    // Normalize to array for consistent handling
    $page_ids = is_array($term_related_page) ? $term_related_page : [$term_related_page];
    $links = [];

    foreach ($page_ids as $page) {
        // Extract ID from post object or use ID directly
        $post_id = is_object($page) ? $page->ID : (int) $page;
        
        // Verify the post exists
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
add_filter('manage_season_custom_column', 'ct_display_season_meta_column', 10, 3);

/**
 * Add "Related Page" field to Quick Edit form
 */
function ct_season_quick_edit_custom_box($column_name, $screen, $taxonomy)
{
    if ($column_name !== 'term_related_page' || $taxonomy !== 'season') {
        return;
    }

    $pages = get_posts([
        'post_type'      => 'page',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);
    
    // Build hierarchical display
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
add_action('quick_edit_custom_box', 'ct_season_quick_edit_custom_box', 10, 3);

/**
 * Build hierarchical page structure
 */
function ct_build_page_hierarchy($pages)
{
    $page_map = [];
    $hierarchy = [];
    
    // Create a map of all pages
    foreach ($pages as $page) {
        $page_map[$page->ID] = $page;
    }
    
    // Build hierarchy
    foreach ($pages as $page) {
        if ($page->post_parent === 0) {
            $hierarchy[$page->ID] = [
                'page'     => $page,
                'children' => ct_get_page_children($page->ID, $page_map),
            ];
        }
    }
    
    return $hierarchy;
}

/**
 * Get children of a page recursively
 */
function ct_get_page_children($parent_id, $page_map)
{
    $children = [];
    
    foreach ($page_map as $page) {
        if ($page->post_parent === $parent_id) {
            $children[$page->ID] = [
                'page'     => $page,
                'children' => ct_get_page_children($page->ID, $page_map),
            ];
        }
    }
    
    return $children;
}

/**
 * Display pages with hierarchy indentation
 */
function ct_display_page_hierarchy($hierarchy, $depth = 0)
{
    foreach ($hierarchy as $item) {
        $page = $item['page'];
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $depth);
        ?>
        <option value="<?php echo esc_attr($page->ID); ?>">
            <?php echo $indent; ?><?php echo esc_html($page->post_title ?: "(Untitled #$page->ID)"); ?>
        </option>
        <?php
        
        // Recursively display children
        if (!empty($item['children'])) {
            ct_display_page_hierarchy($item['children'], $depth + 1);
        }
    }
}

/**
 * Populate Quick Edit form with current meta value
 */
function ct_season_edit_form_load()
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
add_action('admin_footer', 'ct_season_edit_form_load');

/**
 * Save term_related_page meta when term is updated
 */
function ct_season_save_term_meta($term_id, $tt_id, $taxonomy)
{
    if ($taxonomy !== 'season' || !isset($_POST['term_related_page_id'])) {
        return;
    }

    if (!isset($_POST['_wpnonce_edit-tag']) || !wp_verify_nonce($_POST['_wpnonce_edit-tag'], 'edit-tag')) {
        return;
    }

    $post_id = (int) $_POST['term_related_page_id'];

    if ($post_id > 0) {
        update_term_meta($term_id, 'term_related_page', $post_id);
    } else {
        delete_term_meta($term_id, 'term_related_page');
    }
}
add_action('edit_season', 'ct_season_save_term_meta', 10, 3);
