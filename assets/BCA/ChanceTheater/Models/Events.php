<?php
/**
 * Chance Theater WordPress Template
 *
 * @package    wordpress/wordpress
 * @subpackage chancetheater/website
 * @author     Brodkin CyberArts <info@brodkinca.com>
 * @copyright  2015 Brodkin CyberArts
 * @version    Git: $Id: 5b19d2f23e5ff8f1002cb29fdd0eef422c480e23 $
 * @link       http://chancetheater.com/
 */

namespace BCA\ChanceTheater\Models;

use \WP_Query;

/**
 * CT Events Model
 */
class Events
{
    /**
     * Get Events by Production
     *
     * @param string|integer $prod_id Production Post ID.
     * @param string|integer $count   Number of records to retrieve.
     * @param string|integer $offset  Offset start of records by X.
     *
     * @return array
     */
    public static function getProduction(int $prod_id, int $count = 10, int $offset = 0)
    {
        $queries = ['key'=> 'production', 'value'=> $prod_id];
        $queries[] = ['key'=> 'is-promo', 'value'=> '1', 'compare'=> 'NOT EXISTS'];

        $posts_query = new WP_Query([
            'post_type'=> 'ct-event',
            'posts_per_page'=> $count,
            'offset'=> $offset,
            'meta_key'=> 'date-start',
            'order' => 'ASC',
            'meta_query'=> $queries
        ]);
        $posts = $posts_query->posts;

        return $posts_query->posts;
    }

    /**
     * Get Events by Production
     *
     * @param string|integer $prod_id Production Post ID.
     * @param string|integer $count   Number of records to retrieve.
     * @param string|integer $offset  Offset start of records by X.
     *
     * @return array
     */
    public static function getProductionFuture(int $prod_id, int $count = 10, int $offset = 0)
    {
        $queries[] = ['key'=> 'production', 'value'=> $prod_id];
        $queries[] = ['key'=> 'date-start', 'value'=> time(), 'compare'=> '>'];
        $queries[] = ['key'=> 'is-promo', 'value'=> '1', 'compare'=> 'NOT EXISTS'];

        $posts_query = new WP_Query([
            'post_type'=> 'ct-event',
            'posts_per_page'=> $count,
            'offset'=> $offset,
            'meta_key'=> 'date-start',
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_query'=> $queries
        ]);

        return $posts_query;
    }

    /**
     * Get Events by Production
     *
     * @param string|integer $prod_id Production Post ID.
     * @param string|integer $count   Number of records to retrieve.
     * @param string|integer $offset  Offset start of records by X.
     *
     * @return array
     */
    public static function getProductionPromos(int $prod_id, int $count = 10, int $offset = 0)
    {
        $queries[] = ['key'=> 'production', 'value'=> $prod_id];
        $queries[] = ['key'=> 'date-start', 'value'=> time(), 'compare'=> '>'];
        $queries[] = ['key'=> 'is-promo', 'value'=> '1'];

        $posts_query = new WP_Query([
            'post_type'=> 'ct-event',
            'posts_per_page'=> $count,
            'offset'=> $offset,
            'meta_key'=> 'date-start',
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_query'=> $queries
        ]);
        $posts = $posts_query->posts;

        return $posts_query;
    }
}
