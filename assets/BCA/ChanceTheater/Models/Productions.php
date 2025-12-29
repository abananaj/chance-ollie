<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: c014745a9854d519bb5f0996955bb11222d6aea0 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Models;

use \WP_Query;

/**
 * CT Productions Model
 */
class Productions
{
    const FILTER_NOSEASON = 'noseason';

    /**
     * Get productions that are open or opening in the future.
     *
     * @param string|integer $count  Number of records to retrieve.
     * @param string|integer $offset Offset start of records by X.
     * @param string|integer $season Filter productions by X season.
     *
     * @return array                  Array of posts.
     */
    public static function getProductionsFuture($count = 5, $offset = 0, $season = null)
    {
        $args = [
            'post_type' => 'ct-production',
            'meta_key' => 'date-opening',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'posts_per_page' => $count,
            'offset' => $offset,
            'meta_query' => [
                [
                    'key'=> 'date-closing',
                    'value'=> time(),
                    'compare'=> '>='
                ]
            ]
        ];

        if ($season === self::FILTER_NOSEASON) {
            $args['tax_query'][] = [
                'taxonomy' => 'season',
                'terms' => get_terms('season', ['fields' => 'ids']),
                'operator' => 'NOT IN'
            ];
        } elseif ($season) {
            $args['tax_query'][] = [
                'taxonomy' => 'season',
                'terms' => [$season],
                'operator' => 'IN'
            ];
        }

        $query = new WP_Query($args);

        return $query->posts;
    }

    /**
     * Get productions in a given season.
     *
     * @param string $slug Season slug.
     *
     * @return WP_Query     Query object for matching posts.
     */
    public static function getProductionsSeason(string $slug)
    {
        $args = [
            'post_type' => 'ct-production',
            'meta_key' => 'date-opening',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'tax_query' => [
                [
                    'taxonomy' => 'season',
                    'field' => 'slug',
                    'terms' => $slug,
                    'compare' => 'NOT IN'
                ]
            ]
        ];

        $query = new WP_Query($args);

        return $query;
    }

    /**
     * Get quotes for a given production.
     *
     * @param string|integer $post_id Post ID for which quotes should be retrieved.
     * @param string|integer $count   Number of records to retrieve.
     * @param string|integer $offset  Offset start of records by X.
     *
     * @return array            Array of posts.
     */
    public static function getProductionQuotes($post_id, $count = 5, $offset = 0)
    {
        $query = new WP_Query([
            'post_type' => 'post',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'posts_per_page' => $count,
            'offset' => $offset,
            'meta_query' => [
            [
                    'key' => 'production',
                    'value' => $post_id
                ]
            ],
            'tax_query' => [
            [
                  'taxonomy' => 'post_format',
                  'field' => 'slug',
                  'terms' => 'post-format-quote'
                ]
            ]
        ]);

        return $query->posts;
    }

    /**
     * Get posts related to a given post.
     *
     * @param string|integer $post_id ID of post for which related posts should be retrieved.
     * @param string|integer $count   Number of records to retrieve.
     * @param string|integer $offset  Offset start of records by X.
     *
     * @return WP_Query         Query object for matching posts.
     */
    public static function getRelatedPosts($post_id, $count = 3, $offset = 0)
    {
        $query = new WP_Query([
            'post_type' => 'post',
            'posts_per_page' => $count,
            'offset' => $offset,
            'meta_query' => [
            [
                    'key' => 'production',
                    'value' => $post_id
                ]
            ]
        ]);

        return $query;
    }
}
