<?php

/**
 * Post Duplication Functionality
 * 
 * Adds a "Duplicate" action to post lists that allows users to:
 * - Create a copy of a post with all metadata and ACF fields
 * - Go straight to editing in the block editor
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
add_action('admin_init', 'ct_handle_duplicate_post_action');
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

	$post_id = intval($_GET['post']);
	$post = get_post($post_id);

	// Validate post exists and is supported
	if (!$post || !in_array($post->post_type, $duplicate_post_types)) {
		wp_safe_remote_post(admin_url('admin.php?page=duplicate_error'));
		return;
	}

	// Verify nonce
	if (!wp_verify_nonce($_GET['_wpnonce'], 'duplicate_post_' . $post_id)) {
		wp_die(__('Security check failed'));
	}

	// Verify user can edit posts
	if (!current_user_can('edit_posts', $post_id)) {
		wp_die(__('You do not have permission to duplicate this post'));
	}

	// Duplicate the post
	$new_post_id = ct_duplicate_post($post_id);

	if (!$new_post_id) {
		wp_die(__('Failed to duplicate post'));
	}

	// Redirect to block editor
	$edit_url = add_query_arg(
		array('postId' => $new_post_id),
		admin_url('post.php?post=' . $new_post_id . '&action=edit')
	);

	wp_redirect($edit_url);
	exit;
}

/**
 * Duplicate a post with all its metadata and ACF fields
 *
 * @param int $post_id The ID of the post to duplicate
 * @return int|false The ID of the new post or false on failure
 */
function ct_duplicate_post($post_id)
{
	$post = get_post($post_id);

	if (!$post) {
		return false;
	}

	// Prepare the new post data
	$new_post = array(
		'post_title' => $post->post_title . ' (Copy)',
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'post_status' => 'draft', // Set to draft so user can review before publishing
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
	$featured_image_id = get_post_thumbnail_id($post_id);
	if ($featured_image_id) {
		set_post_thumbnail($new_post_id, $featured_image_id);
	}

	// Copy all post meta (including ACF fields)
	$post_meta = get_post_meta($post_id);

	foreach ($post_meta as $meta_key => $meta_values) {
		// Skip protected meta (starts with _)
		if (substr($meta_key, 0, 1) === '_') {
			continue;
		}

		foreach ($meta_values as $meta_value) {
			// ACF serializes data, so we use maybe_unserialize to handle it properly
			$meta_value = maybe_unserialize($meta_value);
			add_post_meta($new_post_id, $meta_key, $meta_value);
		}
	}

	// Copy taxonomies
	$taxonomies = get_object_taxonomies($post->post_type);
	foreach ($taxonomies as $taxonomy) {
		$terms = wp_get_post_terms($post_id, $taxonomy, array('fields' => 'ids'));
		if (!is_wp_error($terms) && !empty($terms)) {
			wp_set_post_terms($new_post_id, $terms, $taxonomy);
		}
	}

	// Action hook for custom duplication logic
	do_action('after_duplicate_post', $new_post_id, $post_id);

	return $new_post_id;
}
