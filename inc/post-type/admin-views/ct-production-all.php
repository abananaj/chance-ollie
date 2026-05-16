<?php

/**
 * Customize ct-production admin columns to show opening/closing dates
 * instead of the post date.
 */

/**
 * Modify admin columns for ct-production list table.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function ct_production_columns($columns)
{
  $new_columns = array();

  $new_columns['cb']       = isset($columns['cb']) ? $columns['cb'] : '';
  $new_columns['title']    = isset($columns['title']) ? $columns['title'] : '';
  $new_columns['opening']  = __('Opening', 'chance-theater');
  $new_columns['series']   = isset($columns['series']) ? $columns['series'] : __('Series', 'chance-theater');
  $new_columns['season']   = isset($columns['season']) ? $columns['season'] : __('Season', 'chance-theater');
  $new_columns['post_tag'] = isset($columns['post_tag']) ? $columns['post_tag'] : __('Tags', 'chance-theater');

  return $new_columns;
}
add_filter('manage_edit-ct-production_columns', 'ct_production_columns');

/**
 * Build taxonomy term links for production admin columns.
 *
 * @param int    $post_id  Post ID.
 * @param string $taxonomy Taxonomy slug.
 * @return string Comma-separated links or empty string.
 */
function ct_production_column_term_links($post_id, $taxonomy)
{
  $terms = get_the_terms($post_id, $taxonomy);
  if (! $terms || is_wp_error($terms)) {
    return '';
  }
  $links = array_map(
    function ($term) use ($taxonomy) {
      $qv = get_taxonomy($term->taxonomy)->query_var ?: $term->taxonomy;
      return '<a href="' . esc_url(admin_url('edit.php?post_type=ct-production&' . $qv . '=' . $term->slug)) . '">' . esc_html($term->name) . '</a>';
    },
    $terms
  );
  return implode(', ', $links);
}

/**
 * Display custom column content for ct-production list table.
 *
 * @param string $column  Column slug.
 * @param int    $post_id Post ID.
 */
function ct_production_column_content($column, $post_id)
{
  if (in_array($column, array('season', 'series', 'post_tag'), true)) {
    $taxonomy = ('post_tag' === $column) ? 'post_tag' : $column;
    $links    = ct_production_column_term_links($post_id, $taxonomy);
    if ($links) {
      echo wp_kses_post($links);
    }
  } elseif ('opening' === $column) {
    $value = get_post_meta($post_id, 'opening', true);
    if ($value) {
      $date = null;

      // Try parsing as datetime string (2026-02-10 08:00:00).
      if (false !== strpos($value, '-') && false !== strpos($value, ':')) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
      } elseif (is_numeric($value) && 8 === strlen($value)) {
        // Try parsing as compact date format (20260910).
        $date = DateTime::createFromFormat('Ymd', $value);
      }

      // Try generic parsing as fallback.
      if (! $date) {
        $date = new DateTime($value);
      }

      if ($date) {
        echo esc_html($date->format('l, F j, Y'));
      } else {
        echo esc_html($value);
      }
    }
  }
}
add_action('manage_ct-production_posts_custom_column', 'ct_production_column_content', 10, 2);

/**
 * Make production columns sortable.
 *
 * @param array $columns Sortable columns.
 * @return array
 */
function ct_production_sortable_columns($columns)
{
  $columns['opening'] = 'opening';
  return $columns;
}
add_filter('manage_edit-ct-production_sortable_columns', 'ct_production_sortable_columns');

/**
 * Set default sort order for ct-production list table.
 *
 * @param WP_Query $query Current query.
 * @return WP_Query
 */
function ct_production_default_sort($query)
{
  if (! is_admin() || 'ct-production' !== $query->get('post_type')) {
    return $query;
  }

  if (! isset($_GET['orderby'])) {
    $query->set('orderby', 'opening');
    $query->set('order', 'desc');
  }

  return $query;
}
add_filter('pre_get_posts', 'ct_production_default_sort');

/**
 * Add season and series filter dropdowns to ct-production list table.
 */
function ct_production_filter_dropdowns()
{
  global $post_type;

  if ('ct-production' !== $post_type) {
    return;
  }

  $terms = get_terms(array(
    'taxonomy'   => 'season',
    'hide_empty' => false,
  ));

  if (! empty($terms) && ! is_wp_error($terms)) {
    $selected_season = isset($_GET['season']) ? sanitize_text_field($_GET['season']) : '';

    echo '<select name="season" id="season-filter">';
    echo '<option value="">All Seasons</option>';
    foreach ($terms as $term) {
      $selected = selected($selected_season, $term->slug, false);
      echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
    }
    echo '</select>';
  }

  $series_terms = get_terms(array(
    'taxonomy'   => 'series',
    'hide_empty' => false,
  ));

  if (! empty($series_terms) && ! is_wp_error($series_terms)) {
    $selected_series = isset($_GET['series']) ? sanitize_text_field($_GET['series']) : '';

    echo '<select name="series" id="series-filter">';
    echo '<option value="">All Series</option>';
    foreach ($series_terms as $term) {
      $selected = selected($selected_series, $term->slug, false);
      echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
    }
    echo '</select>';
  }
}
add_action('restrict_manage_posts', 'ct_production_filter_dropdowns');

/**
 * Hide the "All Dates" filter for ct-production list table.
 */
function ct_production_hide_date_filter()
{
  global $post_type;

  if ('ct-production' !== $post_type) {
    return;
  }

  echo '<style>select[name="m"] { display: none; }</style>';
}
add_action('admin_head', 'ct_production_hide_date_filter');
