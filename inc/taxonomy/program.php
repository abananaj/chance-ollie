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

/**
 * Add "Related Page" column to program taxonomy list
 */
function ct_add_program_columns($columns)
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
add_filter('manage_edit-program_columns', 'ct_add_program_columns');

/**
 * Display term meta value in the "Related Page" column for program
 */
function ct_display_program_meta_column($content, $column_name, $term_id)
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
add_filter('manage_program_custom_column', 'ct_display_program_meta_column', 10, 3);

/**
 * Add "Related Page" field to Quick Edit form for program
 */
function ct_program_quick_edit_custom_box($column_name, $screen, $taxonomy)
{
    if ($column_name !== 'term_related_page' || $taxonomy !== 'program') {
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
add_action('quick_edit_custom_box', 'ct_program_quick_edit_custom_box', 10, 3);

/**
 * Populate Quick Edit form with current meta value for program
 */
function ct_program_edit_form_load()
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
add_action('admin_footer', 'ct_program_edit_form_load');

/**
 * Save term_related_page meta when program term is updated
 */
function ct_program_save_term_meta($term_id, $tt_id, $taxonomy)
{
    if ($taxonomy !== 'program' || !isset($_POST['term_related_page_id'])) {
        return;
    }

    $post_id = (int) $_POST['term_related_page_id'];
    
    if ($post_id > 0) {
        update_term_meta($term_id, 'term_related_page', $post_id);
    } else {
        delete_term_meta($term_id, 'term_related_page');
    }
}
add_action('edit_program', 'ct_program_save_term_meta', 10, 3);
