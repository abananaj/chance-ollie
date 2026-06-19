<?php

/**
 * ORDER BY META KEY
 *
 * Extends core/query block with a meta sort inspector panel.
 * Supports string, number, date-time, and boolean types with
 * appropriate WP_Query orderby values.
 *
 * @see https://developer.wordpress.org/apis/hooks/filter-reference
 * @see https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters
 * @see https://developer.wordpress.org/reference/classes/wp_meta_query/
 */

// Register custom attributes on core/query block server-side.
add_filter('register_block_type_args', function ($args, $block_type) {
	if ($block_type !== 'core/query') return $args;
	$args['attributes'] += [
		'orderMetaKey'       => ['type' => 'string', 'default' => ''],
		'orderMetaType'      => ['type' => 'string', 'default' => 'string'],
		'orderMetaDirection' => ['type' => 'string', 'default' => 'ASC'],
	];
	return $args;
}, 10, 2);

add_action('enqueue_block_editor_assets', function () {
	$js_path = get_stylesheet_directory() . '/inc/metadata/js/order-by-editor.js';
	wp_enqueue_script(
		'chance-order-by-meta-editor',
		get_stylesheet_directory_uri() . '/inc/metadata/js/order-by-editor.js',
		['wp-hooks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-compose'],
		file_exists($js_path) ? filemtime($js_path) : '1.0',
		true
	);
});

/**
 * Apply meta ordering to core/query block queries when orderMetaKey is set.
 *
 * Uses 'meta_value_num' for numeric and date types so numeric/date ordering
 * is correct. Uses 'meta_value' for strings and booleans.
 * YYYYMMDD dates sort correctly as either strings or numbers since the
 * format is lexicographically ordered.
 *
 * @param array    $query WP_Query args
 * @param WP_Block $block The core/query block instance
 * @param int      $page  Current page number
 */
add_filter('query_loop_block_query_vars', function ($query, $block, $page) {
	$attrs     = $block->attributes ?? [];
	$meta_key  = trim($attrs['orderMetaKey'] ?? '');
	$meta_type = $attrs['orderMetaType'] ?? 'string';
	$direction = strtoupper(trim($attrs['orderMetaDirection'] ?? 'ASC'));

	if (empty($meta_key)) return $query;

	// Whitelist direction
	if (!in_array($direction, ['ASC', 'DESC'], true)) return $query;

	// Numeric orderby for number and date-time types; string orderby for others
	$orderby = in_array($meta_type, ['number', 'date-time'], true)
		? 'meta_value_num'
		: 'meta_value';

	$query['meta_key'] = sanitize_key($meta_key);
	$query['orderby']  = $orderby;
	$query['order']    = $direction;

	return $query;
}, 10, 3);
