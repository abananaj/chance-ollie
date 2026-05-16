<?php

namespace Chance\Models;

class Supporters
{

    /**
     * Get random Supporters.
     *
     * @param int $qty Number of posts to retrieve.
     *
     * @return \WP_Query
     */
    public static function get_random_supporters($qty = 1)
    {
        $meta_query = array(
            array('key' => 'display-sidebar', 'value' => true),
        );

        $posts_query = new \WP_Query(array(
            'orderby'        => 'rand',
            'posts_per_page' => $qty,
            'post_type'      => 'ct-supporter',
            'meta_query'     => $meta_query,
        ));

        return $posts_query;
    }
}
