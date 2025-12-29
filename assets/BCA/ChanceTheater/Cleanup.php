<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: d5a5d630d02d1454340cb55fd6622f38e74c7f51 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater;

use \WP_Post;
use \WP_Query;

/**
 * Render a calendar of events.
 */
class Cleanup
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        add_action('trashed_post', __CLASS__.'::cleanupPost', 10);
    }

    /**
     * Clean up data for a post.
     *
     * @param integer $post_id Post ID.
     *
     * @return mixed
     */
    public static function cleanupPost(int $post_id)
    {
        $post = get_post($post_id);

        if ($post->post_type == 'ct-production') {
            return self::cleanupProduction($post);
        }
    }

    /**
     * Delete production credits for a deleted post.
     *
     * @param WP_Post $post Post ID.
     *
     * @return void
     */
    private static function cleanupProduction(WP_Post $post)
    {
        $q = new WP_Query([
            'post_type' => 'ct-production-role',
            'meta_key' => 'production',
            'meta_value' => $post->ID,
            'fields' => 'ids'
        ]);

        foreach ($q->posts as $post_id) {
            wp_trash_post($post_id);
        }
    }
}

