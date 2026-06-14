<?php

/**
 * Post Duplication Functionality
 *
 * Adds a "Duplicate" action to post lists that allows users to:
 * - Create a copy of a post (content only by default)
 * - Optionally copy: featured image, taxonomies, metadata, ACF fields
 * - Go straight to editing in the block editor
 *
 * USAGE:
 * By default, only content (title, body, excerpt) is copied.
 *
 * To customize what gets copied, use the 'ct_duplicate_post_copy_options' filter:
 *
 *   add_filter('ct_duplicate_post_copy_options', function($options, $post_id, $post) {
 *       if ($post->post_type === 'production') {
 *           $options['featured_image'] = true;
 *           $options['taxonomies'] = true;
 *           $options['acf'] = true;
 *       }
 *       return $options;
 *   }, 10, 3);
 *
 * Available options:
 *   - content: true/false (title, body, excerpt)
 *   - featured_image: true/false
 *   - taxonomies: true/false (categories, tags, etc.)
 *   - metadata: true/false (non-ACF post meta)
 *   - acf: true/false (ACF fields)
 */

// List of custom post types that support duplication
function get_duplicate_post_types()
{
	return array('post', 'page', 'artist', 'class', 'credit', 'event', 'production', 'supporter', 'venue');
}

/**
 * Add duplicate action link to post row actions
 */
add_filter('post_row_actions', 'ct_add_duplicate_action', 10, 2);
add_filter('page_row_actions', 'ct_add_duplicate_action', 10, 2);
function ct_add_duplicate_action($actions, $post)
{
	$duplicate_post_types = get_duplicate_post_types();

	// Only show for supported post types
	if (!in_array($post->post_type, $duplicate_post_types)) {
		return $actions;
	}

	// Only show for users who can edit posts
	if (!current_user_can('edit_posts', $post->ID)) {
		return $actions;
	}

	$duplicate_url = wp_nonce_url(
		add_query_arg(
			array(
				'action' => 'duplicate_post',
				'post' => $post->ID,
			),
			admin_url('admin.php')
		),
		'duplicate_post_' . $post->ID
	);

	$actions['duplicate'] = '<a href="' . esc_url($duplicate_url) . '">' . __('Duplicate', 'chance-theme') . '</a>';

	return $actions;
}

/**
 * Handle the duplicate post action
 */
add_action('admin_init', 'ct_handle_duplicate_post_action', 1);
function ct_handle_duplicate_post_action()
{
	$duplicate_post_types = get_duplicate_post_types();

	// Check if this is a duplicate action request
	if (
		!isset($_GET['action']) ||
		$_GET['action'] !== 'duplicate_post' ||
		!isset($_GET['post']) ||
		!isset($_GET['_wpnonce'])
	) {
		return;
	}

	// Safety check: verify this is the first execution
	$post_id = intval($_GET['post']);
	$execution_key = 'duplicate_post_' . $post_id . '_' . get_current_user_id();

	if (get_transient($execution_key)) {
		wp_die(__('Duplicate action already in progress. Please refresh the page.'));
	}
	set_transient($execution_key, 1, 30);

	$post = get_post($post_id);

	// Validate post exists and is supported
	if (!$post || !in_array($post->post_type, $duplicate_post_types)) {
		wp_die(__('Post type not supported for duplication'));
	}

	// Verify nonce
	if (!wp_verify_nonce($_GET['_wpnonce'], 'duplicate_post_' . $post_id)) {
		wp_die(__('Security check failed'));
	}

	// Verify user can edit posts
	if (!current_user_can('edit_post', $post_id)) {
		wp_die(__('You do not have permission to duplicate this post'));
	}

	// Determine what to copy based on filter
	$copy_options = apply_filters('ct_duplicate_post_copy_options', array(
		'content' => true,
		'featured_image' => false,
		'taxonomies' => false,
		'metadata' => false,
		'acf' => false,
	), $post_id, $post);

	// Duplicate the post
	$new_post_id = ct_duplicate_post($post_id, $copy_options);

	if (!$new_post_id) {
		wp_die(__('Failed to duplicate post'));
	}

	// Redirect to block editor
	$edit_url = admin_url('post.php?post=' . $new_post_id . '&action=edit');
	wp_redirect($edit_url);
	exit;
}

/**
 * Duplicate a post with configurable options for what to copy
 *
 * @param int $post_id The ID of the post to duplicate
 * @param array $copy_options What to copy: content, featured_image, taxonomies, metadata, acf
 * @return int|false The ID of the new post or false on failure
 */
function ct_duplicate_post($post_id, $copy_options = array())
{
	$post = get_post($post_id);

	if (!$post) {
		return false;
	}

	// Default options
	$copy_options = wp_parse_args($copy_options, array(
		'content' => true,
		'featured_image' => false,
		'taxonomies' => false,
		'metadata' => false,
		'acf' => false,
	));

	// Prepare the new post data
	$new_post = array(
		'post_title' => $post->post_title . ' (Copy)',
		'post_content' => $copy_options['content'] ? $post->post_content : '',
		'post_excerpt' => $copy_options['content'] ? $post->post_excerpt : '',
		'post_status' => 'draft',
		'post_type' => $post->post_type,
		'post_author' => get_current_user_id(),
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
	);

	// Insert the new post
	$new_post_id = wp_insert_post($new_post);

	if (is_wp_error($new_post_id)) {
		return false;
	}

	// Copy featured image
	if ($copy_options['featured_image']) {
		$featured_image_id = get_post_thumbnail_id($post_id);
		if ($featured_image_id) {
			set_post_thumbnail($new_post_id, $featured_image_id);
		}
	}

	// Copy taxonomies
	if ($copy_options['taxonomies']) {
		$taxonomies = get_object_taxonomies($post->post_type);
		foreach ($taxonomies as $taxonomy) {
			$terms = wp_get_post_terms($post_id, $taxonomy, array('fields' => 'ids'));
			if (!is_wp_error($terms) && !empty($terms)) {
				wp_set_post_terms($new_post_id, $terms, $taxonomy);
			}
		}
	}

	// Copy metadata and ACF fields
	if ($copy_options['metadata'] || $copy_options['acf']) {
		$post_meta = get_post_meta($post_id);

		foreach ($post_meta as $meta_key => $meta_values) {
			// Skip protected meta (starts with _)
			if (substr($meta_key, 0, 1) === '_') {
				continue;
			}

			// Check if this is an ACF field
			$is_acf_field = substr($meta_key, 0, 6) === 'field_' || in_array($meta_key, array('_field_data', 'flexible_content'));

			// Skip if ACF field and not copying ACF
			if ($is_acf_field && !$copy_options['acf']) {
				continue;
			}

			// Skip if non-ACF meta and not copying metadata
			if (!$is_acf_field && !$copy_options['metadata']) {
				continue;
			}

			foreach ($meta_values as $meta_value) {
				$meta_value = maybe_unserialize($meta_value);
				add_post_meta($new_post_id, $meta_key, $meta_value);
			}
		}
	}

	return $new_post_id;
}
