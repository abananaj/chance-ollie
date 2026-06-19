<?php

/**
 * Customize position admin columns to show position-type taxonomy.
 */

// Control column order — position-type is auto-added by show_admin_column in the taxonomy
add_filter('manage_edit-position_columns', function ($columns) {
  $new_columns = [];
  $new_columns['cb']                    = $columns['cb'] ?? '';
  $new_columns['title']                 = $columns['title'] ?? '';
  $new_columns['taxonomy-position-type'] = $columns['taxonomy-position-type'] ?? __('Position Type', 'chance-theater');
  $new_columns['date']                  = $columns['date'] ?? '';
  return $new_columns;
});

// Add Position Type filter dropdown
add_action('restrict_manage_posts', function () {
  global $post_type;

  if ($post_type !== 'position') {
    return;
  }

  $terms = get_terms([
    'taxonomy'   => 'position-type',
    'hide_empty' => false,
  ]);

  if (empty($terms) || is_wp_error($terms)) {
    return;
  }

  $selected = isset($_GET['position-type']) ? sanitize_text_field($_GET['position-type']) : '';

  echo '<select name="position-type" id="position-type-filter">';
  echo '<option value="">' . __('All Position Types', 'chance-theater') . '</option>';

  foreach ($terms as $term) {
    printf(
      '<option value="%s" %s>%s</option>',
      esc_attr($term->slug),
      selected($selected, $term->slug, false),
      esc_html($term->name)
    );
  }

  echo '</select>';
});

// Wire up the position-type filter to the query
add_filter('pre_get_posts', function ($query) {
  if (!is_admin() || $query->get('post_type') !== 'position' || !$query->is_main_query()) {
    return $query;
  }

  $position_type = isset($_GET['position-type']) ? sanitize_text_field($_GET['position-type']) : '';

  if ($position_type) {
    $query->set('tax_query', [[
      'taxonomy' => 'position-type',
      'field'    => 'slug',
      'terms'    => $position_type,
    ]]);
  }

  return $query;
});
