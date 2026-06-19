<?php

/**
 * FILTER BY META KEY
 *
 * Extends core/query block with a meta filter inspector panel.
 * Supports string, number, date-time, and boolean types with
 * appropriate WP_Meta_Query compare operators and type casts.
 *
 * @see https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters
 * @see https://developer.wordpress.org/reference/classes/wp_meta_query/
 */

// Register custom attributes on the core/query block server-side so they
// survive block validation and are available in $block->attributes.
add_filter('register_block_type_args', function ($args, $block_type) {
	if ($block_type !== 'core/query') return $args;
	$args['attributes'] += [
		'filterMetaKey'     => ['type' => 'string', 'default' => ''],
		'filterMetaType'    => ['type' => 'string', 'default' => 'string'],
		'filterMetaCompare' => ['type' => 'string', 'default' => '='],
		'filterMetaValue'   => ['type' => 'string', 'default' => ''],
		'dateMetaKey'       => ['type' => 'string', 'default' => ''],
		'dateMetaMode'      => ['type' => 'string', 'default' => 'upcoming'],
	];
	return $args;
}, 10, 2);

add_action('enqueue_block_editor_assets', function () {
	$js_path = get_stylesheet_directory() . '/inc/metadata/js/filter-by-editor.js';
	wp_enqueue_script(
		'chance-filter-by-meta-editor',
		get_stylesheet_directory_uri() . '/inc/metadata/js/filter-by-editor.js',
		['wp-hooks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-compose'],
		file_exists($js_path) ? filemtime($js_path) : '1.0',
		true
	);
});

/**
 * Inject a meta_query clause into core/query block queries when filterMetaKey is set.
 *
 * @param array    $query WP_Query args
 * @param WP_Block $block The core/query block instance
 * @param int      $page  Current page number
 */
add_filter('query_loop_block_query_vars', function ($query, $block, $page) {
	$attrs      = $block->attributes ?? [];
	$meta_key   = trim($attrs['filterMetaKey'] ?? '');
	$meta_type  = $attrs['filterMetaType'] ?? 'string';
	$compare    = strtoupper(trim($attrs['filterMetaCompare'] ?? '='));
	$meta_value = $attrs['filterMetaValue'] ?? '';

	if (empty($meta_key)) return $query;

	// Whitelist compare operators per type to prevent injection
	$allowed = [
		'string'    => ['=', '!=', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS'],
		'number'    => ['=', '!=', '>', '>=', '<', '<=', 'EXISTS', 'NOT EXISTS'],
		'date-time' => ['=', '!=', '>', '>=', '<', '<=', 'EXISTS', 'NOT EXISTS'],
		'boolean'   => ['=', '!=', 'EXISTS', 'NOT EXISTS'],
	];
	if (!in_array($compare, $allowed[$meta_type] ?? $allowed['string'], true)) return $query;

	// WP_Meta_Query 'type' cast per data type
	$type_cast = [
		'string'    => 'CHAR',
		'number'    => 'DECIMAL',
		'date-time' => 'DATETIME', // refined below after normalization
		'boolean'   => 'NUMERIC',
	];

	// Normalize value and refine type cast
	if ($meta_type === 'date-time') {
		$meta_value        = chance_normalize_meta_datetime($meta_value);
		$type_cast['date-time'] = chance_detect_datetime_sql_type($meta_value);
	} elseif ($meta_type === 'boolean') {
		$meta_value = filter_var($meta_value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
	} elseif ($meta_type === 'string') {
		$meta_value = trim($meta_value);
	}

	$clause = [
		'key'     => sanitize_key($meta_key),
		'compare' => $compare,
		'type'    => $type_cast[$meta_type] ?? 'CHAR',
	];

	if (!in_array($compare, ['EXISTS', 'NOT EXISTS'], true)) {
		$clause['value'] = $meta_value;
	}

	$query['meta_query']   = $query['meta_query'] ?? [];
	$query['meta_query'][] = $clause;

	return $query;
}, 10, 3);

/**
 * Apply upcoming/past date filter to core/query block queries when dateMetaKey is set.
 * Compares the meta value against today's date in Ymd format (YYYYMMDD), which
 * matches ACF date_picker storage and sorts correctly as a CHAR comparison.
 * Mode 'all' is a no-op; 'upcoming' filters >= today; 'past' filters < today.
 */
add_filter('query_loop_block_query_vars', function ($query, $block, $page) {
	$attrs    = $block->attributes ?? [];
	$meta_key = trim($attrs['dateMetaKey'] ?? '');
	$mode     = $attrs['dateMetaMode'] ?? 'upcoming';

	if (empty($meta_key) || $mode === 'all') return $query;
	if (!in_array($mode, ['upcoming', 'past'], true)) return $query;

	$today   = date('Ymd');
	$compare = $mode === 'upcoming' ? '>=' : '<';

	$query['meta_query']   = $query['meta_query'] ?? [];
	$query['meta_query'][] = [
		'key'     => sanitize_key($meta_key),
		'value'   => $today,
		'compare' => $compare,
		'type'    => 'CHAR',
	];

	return $query;
}, 10, 3);

/**
 * Normalize any common datetime string to a SQL-compatible format.
 *
 * Handles:
 *   - Unix timestamps (10-digit integer string)         → YYYY-MM-DD HH:MM:SS
 *   - Compact datetime YYYYMMDDHHmmss (14 digits)       → YYYY-MM-DD HH:MM:SS
 *   - Compact date YYYYMMDD (8 digits, ACF date_picker) → YYYY-MM-DD
 *   - YYYY-MM-DD, HH:MM:SS, YYYY-MM-DD HH:MM:SS        → unchanged
 */
function chance_normalize_meta_datetime(string $value): string {
	$value = trim($value);

	if (ctype_digit($value) && strlen($value) === 10) {
		return date('Y-m-d H:i:s', (int) $value);
	}

	if (preg_match('/^\d{14}$/', $value)) {
		return substr($value, 0, 4) . '-' . substr($value, 4, 2) . '-' . substr($value, 6, 2)
			. ' ' . substr($value, 8, 2) . ':' . substr($value, 10, 2) . ':' . substr($value, 12, 2);
	}

	if (preg_match('/^\d{8}$/', $value)) {
		return substr($value, 0, 4) . '-' . substr($value, 4, 2) . '-' . substr($value, 6, 2);
	}

	return $value;
}

/**
 * Detect the appropriate WP_Meta_Query 'type' value from a normalized datetime string.
 */
function chance_detect_datetime_sql_type(string $value): string {
	if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) return 'TIME';
	if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) return 'DATETIME';
	return 'DATE';
}
