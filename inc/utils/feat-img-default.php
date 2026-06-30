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

    if (function_exists('get_field')) {
        $default_image_id = (int) get_field('options_default_featured_image', 'option');
    } else {
        $default_image_id = (int) get_option('options_default_featured_image');
    }

    if ($default_image_id > 0) {
        set_post_thumbnail($post_id, $default_image_id);
    }
}
