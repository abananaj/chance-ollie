<?php

/**
 * Set default featured image if none is set.
 *
 * @param int $post_id The ID of the post being saved.
 */
function ct_default_featured_image($post_id)
{
    if (empty($post_id) || $post_id <= 0) {
        return;
    }

    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    if (has_post_thumbnail($post_id)) {
        return;
    }

    $default_image_url = 'https://chancetheater.dev/wp-content/uploads/2025/12/stage-lights-placeholder-4-scaled.jpeg';

    static $default_image_id;
    if (!isset($default_image_id)) {
        $default_image_id = attachment_url_to_postid($default_image_url);
    }

    if ($default_image_id > 0) {
        set_post_thumbnail($post_id, $default_image_id);
    }
}
add_action('save_post', 'ct_default_featured_image', 10, 1);
