<?php

/**
 * Customize supporter admin columns to show supporter-type meta field.
 */

// Modify the columns
add_filter('manage_edit-supporter_columns', function ($columns) {
  $new_columns = [];

  $new_columns['cb']                = $columns['cb'] ?? '';
  $new_columns['title']             = $columns['title'] ?? '';
  $new_columns['supporter-level']   = $columns['supporter-level'] ?? __('Supporter Level', 'chance-theater');
  $new_columns['supporter_type']    = __('Donor Type', 'chance-theater');

  return $new_columns;
});

// Display the columns
add_action('manage_supporter_posts_custom_column', function ($column, $post_id) {
  if ($column === 'supporter-level') {
    $terms = get_the_terms($post_id, 'supporter-level');
    if ($terms && !is_wp_error($terms)) {
      $term_links = array_map(function ($term) {
        $qv = get_taxonomy($term->taxonomy)->query_var ?: $term->taxonomy;
        return '<a href="' . esc_url(admin_url('edit.php?post_type=supporter&' . $qv . '=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
      }, $terms);
      echo implode(', ', $term_links);
    }
  } elseif ($column === 'supporter_type') {
    $value = get_post_meta($post_id, 'supporter-type', true);
    if ($value) {
      echo esc_html(ucfirst($value));
    }
  }
}, 10, 2);

// Make supporter_type sortable
add_filter('manage_edit-supporter_sortable_columns', function ($columns) {
  $columns['supporter_type'] = 'supporter_type';
  return $columns;
});

// Handle meta sorting for supporter_type
add_filter('pre_get_posts', function ($query) {
  if (!is_admin() || $query->get('post_type') !== 'supporter') {
    return $query;
  }

  // Handle meta filter
  if (!empty($_GET['supporter_type_filter'])) {
    $query->set('meta_query', [
      [
        'key'     => 'supporter-type',
        'value'   => sanitize_text_field($_GET['supporter_type_filter']),
        'compare' => '=',
      ],
    ]);
  }

  // Handle meta sorting
  if ($query->get('orderby') === 'supporter_type') {
    $query->set('meta_key', 'supporter-type');
    $query->set('orderby', 'meta_value');
  }

  return $query;
});

// Add supporter-level and supporter-type filter dropdowns
add_action('restrict_manage_posts', function () {
  global $post_type;

  if ($post_type !== 'supporter') {
    return;
  }

  // Supporter Level filter (taxonomy)
  $level_terms = get_terms([
    'taxonomy'   => 'supporter-level',
    'hide_empty' => false,
  ]);

  if (!empty($level_terms) && !is_wp_error($level_terms)) {
    $selected_level = isset($_GET['supporter-level']) ? sanitize_text_field($_GET['supporter-level']) : '';

    echo '<select name="supporter-level" id="supporter-level-filter">';
    echo '<option value="">All Levels</option>';

    foreach ($level_terms as $term) {
      $sel = ($selected_level === $term->slug) ? 'selected' : '';
      echo '<option value="' . esc_attr($term->slug) . '" ' . $sel . '>' . esc_html($term->name) . '</option>';
    }

    echo '</select>';
  }

  // Supporter Type filter (meta)
  $selected = isset($_GET['supporter_type_filter']) ? sanitize_text_field($_GET['supporter_type_filter']) : '';

  $types = [
    'individual'   => 'Individual',
    'institutional' => 'Institutional',
  ];

  echo '<select name="supporter_type_filter" id="supporter-type-filter">';
  echo '<option value="">All Types</option>';

  foreach ($types as $value => $label) {
    $sel = ($selected === $value) ? 'selected' : '';
    echo '<option value="' . esc_attr($value) . '" ' . $sel . '>' . esc_html($label) . '</option>';
  }

  echo '</select>';
});

// Hide the "All Dates" filter for supporter
add_action('admin_head', function () {
  global $post_type;

  if ($post_type !== 'supporter') {
    return;
  }

  echo '<style>select[name="m"] { display: none; }</style>';
});
