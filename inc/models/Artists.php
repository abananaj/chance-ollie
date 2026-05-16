<?php

namespace Chance\Models;

class Artists
{

    /**
     * Get Artists by Production.
     *
     * @param int|string $prod_id  Production Post ID.
     * @param string     $group_id Production Group (e.g. director).
     *
     * @return \WP_Query
     */
    public static function get_production($prod_id, string $group_id = '')
    {
        $meta_query = array();
        $meta_query[] = array('key' => 'production', 'value' => $prod_id);

        if ($group_id) {
            $meta_query[] = array('key' => 'role-group', 'value' => $group_id);
        }

        $artists = new \WP_Query(array(
            'post_type'      => 'ct-credit',
            'posts_per_page' => 75,
            'order'          => 'ASC',
            'orderby'        => 'menu_order title',
            'meta_query'     => $meta_query,
        ));

        return $artists;
    }
}
