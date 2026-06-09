<?php

/**
 * Apply URL GET params as tax_query / orderby to productions query loops.
 *
 * Runs on the `query_loop_block_query_vars` filter, which is applied inside
 * the core/post-template block render callback.  We scope changes to the
 * `production` post type so other query loops on the page are untouched.
 *
 * Supported GET params:
 *   ?season=slug   — filter by the `season` taxonomy
 *   ?series=slug   — filter by the `series` taxonomy
 *   ?orderby=key   — sort (date | date-asc | title | title-desc)
 */

add_filter('query_loop_block_query_vars', 'chance_filter_productions_by_url_params', 10, 3);

/**
 * Modify WP_Query vars for production query loops based on URL params.
 *
 * @param array    $query The WP_Query arguments being assembled.
 * @param WP_Block $block The core/post-template block instance.
 * @param int      $page  The current page number.
 * @return array Modified query args.
 */
function chance_filter_productions_by_url_params($query, $block, $page)
{
  // Only apply to production post type queries.
  $post_type = $query['post_type'] ?? '';
  if ('production' !== $post_type) {
    return $query;
  }

  // Map of URL param → taxonomy name.
  $taxonomy_params = [
    'season' => 'season',
    'series' => 'series',
  ];

  $tax_query = $query['tax_query'] ?? [];

  foreach ($taxonomy_params as $param => $taxonomy) {
    if (empty($_GET[$param])) {
      continue;
    }

    $slug = sanitize_title($_GET[$param]);
    if (! $slug) {
      continue;
    }

    // Verify the term actually exists to prevent unexpected empty results.
    if (! get_term_by('slug', $slug, $taxonomy)) {
      continue;
    }

    $tax_query[] = [
      'taxonomy' => $taxonomy,
      'field'    => 'slug',
      'terms'    => $slug,
    ];
  }

  if (! empty($tax_query)) {
    if (count($tax_query) > 1) {
      $tax_query['relation'] = 'AND';
    }
    $query['tax_query'] = $tax_query;
  }

  // Apply orderby from URL param (whitelist only).
  if (! empty($_GET['orderby'])) {
    $orderby_map = [
      'date'        => ['orderby' => 'date',  'order' => 'DESC'],
      'date-asc'    => ['orderby' => 'date',  'order' => 'ASC'],
      'title'       => ['orderby' => 'title', 'order' => 'ASC'],
      'title-desc'  => ['orderby' => 'title', 'order' => 'DESC'],
    ];

    $orderby_key = sanitize_key($_GET['orderby']);
    if (isset($orderby_map[$orderby_key])) {
      $query['orderby'] = $orderby_map[$orderby_key]['orderby'];
      $query['order']   = $orderby_map[$orderby_key]['order'];
    }
  }

  return $query;
}
