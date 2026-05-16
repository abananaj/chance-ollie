<?php

namespace Chance\Models;

class Productions
{

    const FILTER_NOSEASON = 'noseason';

    /**
     * Get productions that are open or opening in the future.
     *
     * @param int         $count  Number of records to retrieve.
     * @param int         $offset Offset start of records by X.
     * @param string|null $season Filter productions by X season.
     *
     * @return array Array of posts.
     */
    public static function get_productions_future($count = 5, $offset = 0, $season = null)
    {
        $args = array(
            'post_type'      => 'ct-production',
            'meta_key'       => 'date-opening',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
            'posts_per_page' => $count,
            'offset'         => $offset,
            'meta_query'     => array(
                array(
                    'key'     => 'date-closing',
                    'value'   => time(),
                    'compare' => '>=',
                ),
            ),
        );

        if (self::FILTER_NOSEASON === $season) {
            $args['tax_query'][] = array(
                'taxonomy' => 'season',
                'terms'    => get_terms('season', array('fields' => 'ids')),
                'operator' => 'NOT IN',
            );
        } elseif ($season) {
            $args['tax_query'][] = array(
                'taxonomy' => 'season',
                'terms'    => array($season),
                'operator' => 'IN',
            );
        }

        $query = new \WP_Query($args);

        return $query->posts;
    }

    /**
     * Get productions in a given season.
     *
     * @param string $slug Season slug.
     *
     * @return \WP_Query Query object for matching posts.
     */
    public static function get_productions_season(string $slug)
    {
        $args = array(
            'post_type'  => 'ct-production',
            'meta_key'   => 'date-opening',
            'orderby'    => 'meta_value_num',
            'order'      => 'ASC',
            'tax_query'  => array(
                array(
                    'taxonomy' => 'season',
                    'field'    => 'slug',
                    'terms'    => $slug,
                    'compare'  => 'NOT IN',
                ),
            ),
        );

        $query = new \WP_Query($args);

        return $query;
    }

    /**
     * Get quotes for a given production.
     *
     * @param int|string $post_id Post ID for which quotes should be retrieved.
     * @param int        $count   Number of records to retrieve.
     * @param int        $offset  Offset start of records by X.
     *
     * @return array Array of posts.
     */
    public static function get_production_quotes($post_id, $count = 5, $offset = 0)
    {
        $query = new \WP_Query(array(
            'post_type'      => 'post',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'posts_per_page' => $count,
            'offset'         => $offset,
            'meta_query'     => array(
                array(
                    'key'   => 'production',
                    'value' => $post_id,
                ),
            ),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'post_format',
                    'field'    => 'slug',
                    'terms'    => 'post-format-quote',
                ),
            ),
        ));

        return $query->posts;
    }

    /**
     * Get posts related to a given production.
     *
     * @param int|string $post_id ID of post for which related posts should be retrieved.
     * @param int        $count   Number of records to retrieve.
     * @param int        $offset  Offset start of records by X.
     *
     * @return \WP_Query Query object for matching posts.
     */
    public static function get_related_posts($post_id, $count = 3, $offset = 0)
    {
        $query = new \WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => $count,
            'offset'         => $offset,
            'meta_query'     => array(
                array(
                    'key'   => 'production',
                    'value' => $post_id,
                ),
            ),
        ));

        return $query;
    }
}
