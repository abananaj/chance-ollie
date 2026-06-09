<?php

/**
 * Customize class admin columns to show date_start, date_end, programs, and session.
 */

// Modify the columns
add_filter('manage_edit-class_columns', function ($columns) {
  // Create a new array with desired order
  $new_columns = [];

  // Keep checkbox first
  $new_columns['cb'] = $columns['cb'] ?? '';

  // Add columns in desired order: title, date_start, date_end, program, session
  $new_columns['title'] = $columns['title'] ?? '';
  $new_columns['date_start'] = __('Date Start', 'chance-theater');
  $new_columns['program'] = $columns['program'] ?? __('Program', 'chance-theater');
  $new_columns['session'] = __('Session', 'chance-theater');

  return $new_columns;
});

// Display the columns
add_action('manage_class_posts_custom_column', function ($column, $post_id) {
  if ($column === 'program') {
    $terms = get_the_terms($post_id, 'program');
    if ($terms && !is_wp_error($terms)) {
      $term_links = array_map(function ($term) {
        $qv = get_taxonomy($term->taxonomy)->query_var ?: $term->taxonomy;
        return '<a href="' . esc_url(admin_url('edit.php?post_type=class&' . $qv . '=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
      }, $terms);
      echo implode(', ', $term_links);
    }
  } elseif ($column === 'session') {
    $terms = get_the_terms($post_id, 'session');
    if ($terms && !is_wp_error($terms)) {
      $term_links = array_map(function ($term) {
        $qv = get_taxonomy($term->taxonomy)->query_var ?: $term->taxonomy;
        return '<a href="' . esc_url(admin_url('edit.php?post_type=class&' . $qv . '=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
      }, $terms);
      echo implode(', ', $term_links);
    }
  } elseif ($column === 'date_start') {
    $value = get_post_meta($post_id, 'date_start', true);
    if ($value) {
      $date = null;

      // Try parsing as datetime string (2026-02-10 08:00:00)
      if (strpos($value, '-') !== false && strpos($value, ':') !== false) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
      }
      // Try parsing as compact date format (20260910)
      elseif (is_numeric($value) && strlen($value) === 8) {
        $date = DateTime::createFromFormat('Ymd', $value);
      }
      // Try generic parsing as fallback
      if (!$date) {
        $date = new DateTime($value);
      }

      if ($date) {
        echo $date->format('M j, Y');
      } else {
        echo $value;
      }
    }
  }
}, 10, 2);

// Make columns sortable
add_filter('manage_edit-class_sortable_columns', function ($columns) {
  $columns['date_start'] = 'date_start';
  return $columns;
});

// Set default sorting by date_start descending
add_filter('pre_get_posts', function ($query) {
  if (!is_admin() || $query->get('post_type') !== 'class') {
    return $query;
  }

  // Set default sort
  if (!isset($_GET['orderby'])) {
    $query->set('orderby', 'date_start');
    $query->set('order', 'desc');
  }

  return $query;
});

// Add programs filter dropdown
add_action('restrict_manage_posts', function () {
  global $post_type;

  if ($post_type !== 'class') {
    return;
  }

  $terms = get_terms([
    'taxonomy' => 'program',
    'hide_empty' => false,
  ]);

  if (empty($terms) || is_wp_error($terms)) {
    return;
  }

  $selected_program = isset($_GET['program']) ? sanitize_text_field($_GET['program']) : '';

  echo '<select name="program" id="program-filter">';
  echo '<option value="">All Programs</option>';

  foreach ($terms as $term) {
    $selected = ($selected_program === $term->slug) ? 'selected' : '';
    echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
  }

  echo '</select>';

  // Session filter
  $session_terms = get_terms([
    'taxonomy' => 'session',
    'hide_empty' => false,
  ]);

  if (!empty($session_terms) && !is_wp_error($session_terms)) {
    $selected_session = isset($_GET['session']) ? sanitize_text_field($_GET['session']) : '';

    echo '<select name="session" id="session-filter">';
    echo '<option value="">All Sessions</option>';

    foreach ($session_terms as $term) {
      $selected = ($selected_session === $term->slug) ? 'selected' : '';
      echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
    }

    echo '</select>';
  }
});

// Hide the "All Dates" filter for class
add_action('admin_head', function () {
  global $post_type;

  if ($post_type !== 'class') {
    return;
  }

  echo '<style>select[name="m"] { display: none; }</style>';
});
