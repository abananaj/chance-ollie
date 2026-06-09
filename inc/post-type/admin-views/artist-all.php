<?php

/**
 * Customize artist admin columns to show profession and title meta fields.
 */

// Modify the columns
add_filter('manage_edit-artist_columns', function ($columns) {
  $new_columns = [];

  $new_columns['cb'] = $columns['cb'] ?? '';
  $new_columns['title'] = $columns['title'] ?? '';
  $new_columns['profession'] = __('Profession', 'chance-theater');
  $new_columns['artist_title'] = __('Title', 'chance-theater');
  $new_columns['post_tag'] = $columns['post_tag'] ?? __('Tags', 'chance-theater');

  return $new_columns;
});

// Display the columns
add_action('manage_artist_posts_custom_column', function ($column, $post_id) {
  if ($column === 'profession') {
    echo esc_html(get_post_meta($post_id, 'profession', true));
  } elseif ($column === 'artist_title') {
    echo esc_html(get_post_meta($post_id, 'title', true));
  } elseif ($column === 'post_tag') {
    $terms = get_the_terms($post_id, 'post_tag');
    if ($terms && !is_wp_error($terms)) {
      $term_links = array_map(function ($term) {
        return '<a href="' . esc_url(admin_url('edit.php?post_type=artist&tag=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
      }, $terms);
      echo implode(', ', $term_links);
    }
  }
}, 10, 2);

// Make columns sortable
add_filter('manage_edit-artist_sortable_columns', function ($columns) {
  $columns['profession'] = 'profession';
  $columns['artist_title'] = 'artist_title';
  return $columns;
});

// Add tag filter dropdown
add_action('restrict_manage_posts', function () {
  global $post_type;

  if ($post_type !== 'artist') {
    return;
  }

  $terms = get_terms([
    'taxonomy' => 'post_tag',
    'hide_empty' => true,
    'object_ids' => get_posts(['post_type' => 'artist', 'fields' => 'ids', 'posts_per_page' => -1, 'suppress_filters' => true]),
  ]);

  if (empty($terms) || is_wp_error($terms)) {
    return;
  }

  $selected_tag = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';

  echo '<select name="tag" id="tag-filter">';
  echo '<option value="">All Tags</option>';

  foreach ($terms as $term) {
    $selected = ($selected_tag === $term->slug) ? 'selected' : '';
    echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
  }

  echo '</select>';
});

// Hide the "All Dates" filter for artist
add_action('admin_head', function () {
  global $post_type;

  if ($post_type !== 'artist') {
    return;
  }

  echo '<style>select[name="m"] { display: none; }</style>';
});
