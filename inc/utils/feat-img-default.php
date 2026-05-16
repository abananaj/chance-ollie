<?php

/* 
 * Set default featured image if none is set.
 *
 * @param int $post_id The ID of the post being saved.
 */

function ct_default_featured_image()
{
    if (!has_post_thumbnail()) {
        $default_image_url = 'https://chancetheater.dev/wp-content/uploads/2025/12/stage-lights-placeholder-4-scaled.jpeg';
        $default_image_id = attachment_url_to_postid($default_image_url);
        set_post_thumbnail(get_the_ID(), $default_image_id);
    }
}
add_action('save_post', 'ct_default_featured_image');
