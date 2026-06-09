<?php

/**
 * Customize event admin columns to show event dates and related info.
 */

// Modify the columns
add_filter('manage_edit-event_columns', function ($columns) {
  // Create a new array with desired order
  $new_columns = [];

  // Keep checkbox first
  $new_columns['cb'] = $columns['cb'] ?? '';

  // Add columns in desired order: title, date_start, date_end, event_type, season
  $new_columns['title'] = $columns['title'] ?? '';
  $new_columns['category'] = $columns['category'] ?? __('Category', 'chance-theater');
  $new_columns['event-type'] = $columns['event-type'] ?? __('Event Type', 'chance-theater');
  $new_columns['season'] = $columns['season'] ?? __('Season', 'chance-theater');
  $new_columns['date_start'] = __('Date', 'chance-theater');

  return $new_columns;
});

// Display the columns
add_action('manage_event_posts_custom_column', function ($column, $post_id) {
  if ($column === 'event-type') {
    $terms = get_the_terms($post_id, 'event-type');
    if ($terms && !is_wp_error($terms)) {
      $term_links = array_map(function ($term) {
        $qv = get_taxonomy($term->taxonomy)->query_var ?: $term->taxonomy;
        return '<a href="' . esc_url(admin_url('edit.php?post_type=event&' . $qv . '=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
      }, $terms);
      echo implode(', ', $term_links);
    }
  } elseif ($column === 'category') {
    $terms = get_the_terms($post_id, 'category');
    if ($terms && !is_wp_error($terms)) {
      $term_links = array_map(function ($term) {
        $qv = get_taxonomy($term->taxonomy)->query_var ?: $term->taxonomy;
        return '<a href="' . esc_url(admin_url('edit.php?post_type=event&' . $qv . '=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
      }, $terms);
      echo implode(', ', $term_links);
    }
  } elseif ($column === 'season') {
    $terms = get_the_terms($post_id, 'season');
    if ($terms && !is_wp_error($terms)) {
      $term_links = array_map(function ($term) {
        $qv = get_taxonomy($term->taxonomy)->query_var ?: $term->taxonomy;
        return '<a href="' . esc_url(admin_url('edit.php?post_type=event&' . $qv . '=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
      }, $terms);
      echo implode(', ', $term_links);
    }
  } elseif ($column === 'date_start') {
    $value = get_post_meta($post_id, 'date', true);
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
      // Try parsing as Unix timestamp
      elseif (is_numeric($value)) {
        $date = new DateTime('@' . $value);
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
  } elseif ($column === 'date_end') {
    $value = get_post_meta($post_id, 'date-end', true);
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
      // Try parsing as Unix timestamp
      elseif (is_numeric($value)) {
        $date = new DateTime('@' . $value);
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
add_filter('manage_edit-event_sortable_columns', function ($columns) {
  $columns['date_start'] = 'date-start';
  $columns['date_end'] = 'date-end';
  return $columns;
});

// Set default sorting by date_start descending
add_filter('pre_get_posts', function ($query) {
  if (!is_admin() || $query->get('post_type') !== 'event') {
    return $query;
  }

  // Set default sort
  if (!isset($_GET['orderby'])) {
    $query->set('orderby', 'meta_value');
    $query->set('meta_key', 'date');
    $query->set('order', 'desc');
  }

  return $query;
});

// Add event-type filter dropdown
add_action('restrict_manage_posts', function () {
  global $post_type;

  if ($post_type !== 'event') {
    return;
  }

  $terms = get_terms([
    'taxonomy' => 'event-type',
    'hide_empty' => false,
  ]);

  if (empty($terms) || is_wp_error($terms)) {
    return;
  }

  $selected_event_type = isset($_GET['event-type']) ? sanitize_text_field($_GET['event-type']) : '';

  echo '<select name="event-type" id="event-type-filter">';
  echo '<option value="">All Event Types</option>';

  foreach ($terms as $term) {
    $selected = ($selected_event_type === $term->slug) ? 'selected' : '';
    echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
  }

  echo '</select>';

  // Season filter
  $season_terms = get_terms([
    'taxonomy' => 'season',
    'hide_empty' => false,
  ]);

  if (!empty($season_terms) && !is_wp_error($season_terms)) {
    $selected_season = isset($_GET['season']) ? sanitize_text_field($_GET['season']) : '';

    echo '<select name="season" id="season-filter">';
    echo '<option value="">All Seasons</option>';

    foreach ($season_terms as $term) {
      $selected = ($selected_season === $term->slug) ? 'selected' : '';
      echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
    }

    echo '</select>';
  }
});

// Hide the "All Dates" filter for event
add_action('admin_head', function () {
  global $post_type;

  if ($post_type !== 'event') {
    return;
  }

  echo '<style>select[name="m"] { display: none; }</style>';
});
