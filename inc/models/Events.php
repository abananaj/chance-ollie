<?php

namespace Chance\Models;

class Events
{

    /**
     * Get Events by Production.
     *
     * @param int $prod_id Production Post ID.
     * @param int $count   Number of records to retrieve.
     * @param int $offset  Offset start of records by X.
     *
     * @return array
     */
    public static function get_production(int $prod_id, int $count = 10, int $offset = 0)
    {
        $meta_query = array(
            array('key' => 'production', 'value' => $prod_id),
            array('key' => 'is-promo', 'value' => '1', 'compare' => 'NOT EXISTS'),
        );

        $posts_query = new \WP_Query(array(
            'post_type'      => 'ct-event',
            'posts_per_page' => $count,
            'offset'         => $offset,
            'meta_key'       => 'date-start',
            'order'          => 'ASC',
            'meta_query'     => $meta_query,
        ));

        return $posts_query->posts;
    }

    /**
     * Get upcoming Events by Production.
     *
     * @param int $prod_id Production Post ID.
     * @param int $count   Number of records to retrieve.
     * @param int $offset  Offset start of records by X.
     *
     * @return WP_Query
     */
    public static function get_production_future(int $prod_id, int $count = 10, int $offset = 0)
    {
        $meta_query = array(
            array('key' => 'production', 'value' => $prod_id),
            array('key' => 'date-start', 'value' => time(), 'compare' => '>'),
            array('key' => 'is-promo', 'value' => '1', 'compare' => 'NOT EXISTS'),
        );

        $posts_query = new \WP_Query(array(
            'post_type'      => 'ct-event',
            'posts_per_page' => $count,
            'offset'         => $offset,
            'meta_key'       => 'date-start',
            'order'          => 'ASC',
            'orderby'        => 'meta_value_num',
            'meta_query'     => $meta_query,
        ));

        return $posts_query;
    }

    /**
     * Get promo Events by Production.
     *
     * @param int $prod_id Production Post ID.
     * @param int $count   Number of records to retrieve.
     * @param int $offset  Offset start of records by X.
     *
     * @return WP_Query
     */
    public static function get_production_promos(int $prod_id, int $count = 10, int $offset = 0)
    {
        $meta_query = array(
            array('key' => 'production', 'value' => $prod_id),
            array('key' => 'date-start', 'value' => time(), 'compare' => '>'),
            array('key' => 'is-promo', 'value' => '1'),
        );

        $posts_query = new \WP_Query(array(
            'post_type'      => 'ct-event',
            'posts_per_page' => $count,
            'offset'         => $offset,
            'meta_key'       => 'date-start',
            'order'          => 'ASC',
            'orderby'        => 'meta_value_num',
            'meta_query'     => $meta_query,
        ));

        return $posts_query;
    }
}
